<?php

namespace T2PLib\JobLibrary;

class JobAPI
{
    public static function getJobActiveStatus($domain, $jobID, $job)
    {
        $url = "http://localhost:7005/api/Job/getJobStatus/".$domain."/".$jobID;
        $parameters = $job;
        $headers = ['Content-Type: application/json'];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
        $json = json_decode(json_encode($responFromAPI));
        $result = json_decode($json->result);
        return $responFromAPI;
    }

    public static function updateJobStatus($job)
    {
        $url = "http://localhost:7005/api/Job/updateJobStatus";
        $parameters = $job;
        $headers = ['Content-Type: application/json'];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
    }

    public static function updateJobRunningStatus($domain, $jobID)
    {
        $url = "http://localhost:7005/api/Job/updateJobRunningStatus/".$domain."/".$jobID;
        $parameters = [
            'status' => 'Y'
        ];
        $headers = [];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
    }
}