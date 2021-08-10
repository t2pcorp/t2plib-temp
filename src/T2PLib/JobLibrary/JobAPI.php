<?php

namespace T2PLib\JobLibrary;

class JobAPI
{
    private static function getEnvUrl($env) {
        $url = "https://jobs-api.t2p.co.th";
        if ($env == "LOCAL") {
            $url = "http://localhost:7005";
        }
        if ($env == "DEVELOP") {
            $url = "https://dev-jobs-api.t2p.co.th";
        }
        if ($env == "SIT") {
            $url = "https://sit-jobs-api.t2p.co.th";
        }
        if ($env == "TEST") {
            $url = "https://test-jobs-api.t2p.co.th";
        }
        return $url;
    }

    public static function getJobActiveStatus($domain, $jobID, $job, $env)
    {
        $url = self::getEnvUrl($env);
        $url = "$url/api/Job/getJobStatus/".$domain."/".$jobID;
        $parameters = $job;
        $headers = ['Content-Type: application/json'];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
        $json = json_decode(json_encode($responFromAPI));
        $result = json_decode($json->result);
        return $responFromAPI;
    }

    public static function updateJobStatus($job, $env)
    {
        $url = self::getEnvUrl($env);
        $url = "$url/api/Job/updateJobStatus";
        $parameters = $job;
        $headers = ['Content-Type: application/json'];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
    }

    public static function updateJobRunningStatus($domain, $jobID, $env)
    {
        $url = self::getEnvUrl($env);
        $url = "$url/api/Job/updateJobRunningStatus/".$domain."/".$jobID;
        $parameters = [
            'status' => 'Y'
        ];
        $headers = [];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
    }
}