<?php

namespace T2PLib\JobCheckStatus;

use T2PLib\Job\JobObject;
use T2PLib\Job\Notification;
use Carbon\Carbon;
use \T2PLib\JobCheckStatus\StoreJob;

class JobCheckStatus
{
    private static function getEnvUrl($env) {
        $url = "https://job-api.t2p.co.th";
        if ($env == "LOCAL") {
            $url = "http://localhost:7005";
        }
        if ($env == "DEVELOP") {
            $url = "https://dev-job-api.t2p.co.th";
        }
        if ($env == "SIT") {
            $url = "https://sit-job-api.t2p.co.th";
        }
        if ($env == "TEST") {
            $url = "https://test-job-api.t2p.co.th";
        }
        return $url;
    }

    private static function getToken() {
        $config = \T2P\Util\CommonConfig\Config::get("_ENV.*");
        $env = $config->value('_ENV.NAME');
        $env = "LOCAL";

        $urlEnv = self::getEnvUrl($env);
        $url = "$urlEnv/api/login";
        $parameters = [
            'email' => 'test@example.com',
            'password' => '123456789'
        ];
        $headers = [];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
        $json = json_decode(json_encode($responFromAPI));
        $result = json_decode($json->result);
        return $result->token;
    }

    public static function process()
    {
        //Monitor Self Health on AWS DashBoard 
        $jobLib = new \T2PLib\JobLibrary\JobLibrary();
        $jobLib->updateJobDashboard(100, "Success", "MonitorJobCheck", "JOBS:CheckStatus");

        $config = \T2P\Util\CommonConfig\Config::get("_ENV.*");
        $env = $config->value('_ENV.NAME');
        $env = "LOCAL";

        $urlEnv = self::getEnvUrl($env);
        $token = self::getToken();
        $url = "$urlEnv/api/Job/getJobDataList";
        $parameters = [];
        $headers = ['Authorization: Bearer '. $token];
        $method = "GET";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
        $json = json_decode(json_encode($responFromAPI));
        $result = json_decode($json->result);

        $foundJobs = false;
        foreach($result as $data)
        {
            $foundJobs = true;
            $jobs_data = $data->jobs_data;
            $JobConfig = json_decode($jobs_data)->JobConfig;
            $JobExecuteInfo = json_decode($jobs_data)->JobExecuteInfo;

            if ($data->is_active == 'N' || $JobConfig->PeriodType == 'once' && $data->last_jobUpdate && $data->jobs_status == 'success')
            {
                continue;
            }

            $job = StoreJob::store($JobConfig, $data);
            $jobObject = new JobObject($job);
            $status = $jobObject->getStatus();
            $domain = $JobConfig->Domain;
            $jobID = $JobConfig->JobID;
            $now = Carbon::now($JobConfig->TimeZone);
            
            if ($status == 'fail' || $JobConfig->AdditionCondition->Success != $JobExecuteInfo->Success || $JobExecuteInfo->Error)
            {
                $lastNoti = Carbon::parse($data->last_notification, $JobConfig->TimeZone);
                $notiPeriod = $lastNoti->addMinutes($JobConfig->NotiFrequency);
                if ($data->last_notification != null)
                {
                    if ($data->last_jobCheck > $data->last_jobUpdate && $now < $notiPeriod)
                    {
                        continue;
                    }
                }
                $noti = new Notification($jobObject->getJobData(), $jobObject->getSchedule()->getPreviousRun(), $data->last_jobSuccess, $data->last_jobUpdate, $JobExecuteInfo->Error);
            }
                
            $url = "$urlEnv/api/Job/updateJobCheckStatus/".$domain."/".$jobID;
            $parameters = [
                "status" => $status,
                "lastCheck" => $now
            ];
            $headers = [];
            $method = "POST";
            $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
        }
        // if found jobs mean API work properly then just update dashboard
        if ($foundJobs) {
            $jobLib->updateJobDashboard(100, "Success", "MonitorAPI", "JOBS:CheckStatus");
        }
    }
}