<?php

namespace T2PLib\JobLibrary;

use \T2PLib\JobLibrary\JobAPI;

class JobLibrary
{
    public $jobConfig;
    public $jobExecuteInfo;
    public $env;

    public function __construct() {
        $config = \T2P\Util\CommonConfig\Config::get("_ENV.*");
        // print_r($config->value('_ENV.NAME'));
        $this->env = $config->value('_ENV.NAME');
        $this->env = "LOCAL";

        $this->jobConfig = (object) [
            'Domain' => 'default',
            'JobID' => 'default',
            'Name' => 'default',
            'PeriodType' => 'min',
            'PeriodValue' => '1',
            'ScheduleTime' => '00:00:00',
            'ExecuteDuration' => '1',
            'TimeZone' => 'Asia/Bangkok',
            'AdditionCondition' => [
                'Success' => true
            ],
            'SkipCheck' => false,
            'Notification' => [
                'Line' => '',
                'sms' => '',
                'call' => '',
                'mail' => '',
            ],
            'NotiFrequency' => '1',
            'ArchiveLogUnit' => 'D',
            'ArchiveLogValue' => '1'
        ];
        $this->jobExecuteInfo = (object) [
            'Success' => true,
            'Error' => ''
        ];
    }

    public function getDomain()
    {
        return $this->jobConfig->Domain;
    }

    public function getJobID()
    {
        return $this->jobConfig->JobID;
    }

    public function getName()
    {
        return $this->jobConfig->Name;
    }

    public function getPeriodType()
    {
        return $this->jobConfig->PeriodType;
    }

    public function getPeriodValue()
    {
        return $this->jobConfig->PeriodValue;
    }

    public function getScheduleTime()
    {
        return $this->jobConfig->ScheduleTime;
    }

    public function getExecuteDuration()
    {
        return $this->jobConfig->ExecuteDuration;
    }

    public function getTimeZone()
    {
        return $this->jobConfig->TimeZone;
    }

    public function getAdditionCondition()
    {
        return $this->jobConfig->AdditionCondition;
    }

    public function getSkipCheck()
    {
        return $this->jobConfig->SkipCheck;
    }

    public function getNotification()
    {
        return $this->jobConfig->Notification;
    }
    
    public function getNotiFrequency()
    {
        return $this->jobConfig->NotiFrequency;
    }

    public function getArchiveLogUnit()
    {
        return $this->jobConfig->ArchiveLogUnit;
    }

    public function getArchiveLogValue()
    {
        return $this->jobConfig->ArchiveLogValue;
    }
    
    public function setDomain($domain)
    {
        return ($this->jobConfig->Domain = $domain)? true : false;
    }
    
    public function setJobID($jobID)
    {
        return ($this->jobConfig->JobID = $jobID)? true : false;
    }
    
    public function setName($name)
    {
        return ($this->jobConfig->Name = $name)? true : false;
    }
    
    public function setPeriodTypeMin()
    {
        return ($this->jobConfig->PeriodType = 'min')? true : false;
    }
    
    public function setPeriodTypeDaily()
    {
        return ($this->jobConfig->PeriodType = 'daily')? true : false;
    }
    
    public function setPeriodTypeDate()
    {
        return ($this->jobConfig->PeriodValue = 'date')? true : false;
    }
    
    public function setPeriodTypeDateMonth()
    {
        return ($this->jobConfig->PeriodValue = 'datemonth')? true : false;
    }
    
    public function setPeriodTypeOnce()
    {
        return ($this->jobConfig->PeriodValue = 'once')? true : false;
    }
    
    public function setPeriodValue($value)
    {
        return ($this->jobConfig->PeriodValue = $value)? true : false;
    }
    
    public function setScheduleTime($scheduleTime)
    {
        return ($this->jobConfig->ScheduleTime = $scheduleTime)? true : false;
    }
    
    public function setExecuteDuration($executeDuration)
    {
        return ($this->jobConfig->ExecuteDuration = $executeDuration)? true : false;
    }
    
    public function setTimeZone($timeZone)
    {
        return ($this->jobConfig->TimeZone = $timeZone)? true : false;
    }
    
    public function setAdditionCondition($success)
    {
        return ($this->jobConfig->AdditionCondition['Success'] = $success)? true : false;
    }
    
    public function setSkipCheck($skip)
    {
        return ($this->jobConfig->SkipCheck = $skip)? true : false;
    }
    
    public function setLINENotification($lineToken)
    {
        return ($this->jobConfig->Notification['Line'] = $lineToken)? true : false;
    }
    
    public function setSMSNotification($phoneNumber)
    {
        return ($this->jobConfig->Notification['sms'] = $phoneNumber)? true : false;
    }
    
    public function setPhoneNotification($phoneNumber)
    {
        return ($this->jobConfig->Notification['call'] = $phoneNumber)? true : false;
    }
    
    public function setMailNotification($mail)
    {
        return ($this->jobConfig->Notification['mail'] = $mail)? true : false;
    }
    
    public function setNotiFrequency($frequency)
    {
        return ($this->jobConfig->NotiFrequency = $frequency)? true : false;
    }
    
    public function setArchiveLogUnitDay()
    {
        return ($this->jobConfig->ArchiveLogUnit = 'D')? true : false;
    }
    
    public function setArchiveLogUnitMonth()
    {
        return ($this->jobConfig->ArchiveLogUnit = 'M')? true : false;
    }
    
    public function setArchiveLogValue($value)
    {
        return ($this->jobConfig->ArchiveLogValue = $value)? true : false;
    }
    
    public function setSuccess($success)
    {
        return ($this->JobExecuteInfo->Success = $success)? true : false;
    }
    
    public function setError($message)
    {
        return ($this->JobExecuteInfo->Error = $message)? true : false;
    }

    public function getJobActiveStatus()
    {
        $domain = $this->jobConfig->Domain;
        $jobID = $this->jobConfig->JobID;

        $config = new \stdClass();
        $config->JobConfig = $this->jobConfig;
        $config->JobExecuteInfo = $this->jobExecuteInfo;
        $config = json_encode($config);

        return JobAPI::getJobActiveStatus($domain, $jobID, $config, $this->env);
    }

    public function updateJobStatus($errorMessage = '')
    {
        if ($errorMessage != '')
        {
            $this->jobExecuteInfo->Success = false;
            $this->jobExecuteInfo->Error = $errorMessage;
        }
        $config = new \stdClass();
        $config->JobConfig = $this->jobConfig;
        $config->JobExecuteInfo = $this->jobExecuteInfo;
        $config = json_encode($config);

        JobAPI::updateJobStatus($config, $this->env);
    }

    public function updateJobRunningStatus()
    {
        $domain = $this->jobConfig->Domain;
        $jobID = $this->jobConfig->JobID;
        JobAPI::updateJobRunningStatus($domain, $jobID, $this->env);
    }
}