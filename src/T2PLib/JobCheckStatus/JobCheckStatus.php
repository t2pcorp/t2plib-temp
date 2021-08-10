<?php

namespace T2PLib\JobCheckStatus;

use T2PLib\Job\JobObject;
use T2PLib\Job\Notification;
use Carbon\Carbon;
use \T2PLib\JobCheckStatus\StoreJob;
use \T2PLib\JobCheckStatus\CheckType;

class JobCheckStatus
{
    public static function process()
    {
        $url = "http://localhost:7005/api/Job/getJobDataList";
        $parameters = [];
        $headers = [];
        $method = "GET";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
        $json = json_decode(json_encode($responFromAPI));
        $result = json_decode($json->result);

        foreach($result as $data)
        {
            $jobs_data = $data->jobs_data;
            $JobConfig = json_decode($jobs_data)->JobConfig;
            $JobExecuteInfo = json_decode($jobs_data)->JobExecuteInfo;

            if ($data->is_active == 'N')
            {
                continue;
            }

            $job = StoreJob::store($JobConfig, $data);
            $jobObject = new JobObject($job);
            $status = $jobObject->getStatus();
            $domain = $JobConfig->Domain;
            $jobID = $JobConfig->JobID;
            $now = Carbon::now($JobConfig->TimeZone);
            
            if ($status == 'fail' || !$JobConfig->AdditionCondition->Success)
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
                
            $url = "http://localhost:7005/api/Job/updateJobCheckStatus/".$domain."/".$jobID;
            $parameters = [
                "status" => $status,
                "lastCheck" => $now
            ];
            $headers = [];
            $method = "POST";
            $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
        }
    }
}