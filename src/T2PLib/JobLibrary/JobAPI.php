<?php

namespace T2PLib\JobLibrary;

class JobAPI
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
        if ($env == "UAT") {
            $url = "https://test-job-api.t2p.co.th";
        }
        return $url;
    }

    private static function getToken($user) {
        $config = \T2P\Util\CommonConfig\Config::get("_ENV.*");
        $env = $config->value('_ENV.NAME');

        $urlEnv = self::getEnvUrl($env);
        $url = "$urlEnv/api/login";
        $parameters = [
            'email' => $user->email,
            'password' => $user->password
        ];
        $headers = [];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
        $json = json_decode(json_encode($responFromAPI));
        $result = json_decode($json->result);
        return $result->token;
    }

    public static function getJobActiveStatus($domain, $jobID, $user, $job, $env)
    {
        $token = self::getToken($user);
        $url = self::getEnvUrl($env);
        $url = "$url/api/Job/getJobStatus/".$domain."/".$jobID;
        $parameters = $job;
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '. $token
        ];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
        $json = json_decode(json_encode($responFromAPI));
        $result = json_decode($json->result);
        return $responFromAPI;
    }

    public static function updateJobStatus($user, $job, $env)
    {
        $token = self::getToken($user);
        $url = self::getEnvUrl($env);
        $url = "$url/api/Job/updateJobStatus";
        $parameters = $job;
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '. $token
        ];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
    }

    public static function updateJobRunningStatus($domain, $jobID, $user, $env)
    {
        $token = self::getToken($user);
        $url = self::getEnvUrl($env);
        $url = "$url/api/Job/updateJobRunningStatus/".$domain."/".$jobID;
        $parameters = [
            'status' => 'Y'
        ];
        $headers = [
            'Authorization: Bearer '. $token
        ];
        $method = "POST";
        $responFromAPI = \T2P\Util\Util::MakeRequest($url, $parameters, $method, $headers);
    }
}