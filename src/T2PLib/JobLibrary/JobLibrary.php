<?php

namespace T2PLib\JobLibrary;

use \T2PLib\JobLibrary\JobAPI;
use \Exception as ErrorException;
use Aws\CloudWatch\CloudWatchClient; 
use Aws\Exception\AwsException;

class JobLibrary
{
    public $jobConfig;
    public $jobExecuteInfo;
    public $env;
    public $jobConfigHasChange;
    public $jobExecuteInfoHasChange;

    public function __construct() {
        $config = \T2P\Util\CommonConfig\Config::get("_ENV.*");
        // print_r($config->value('_ENV.NAME'));
        $this->env = $config->value('_ENV.NAME');
        $this->env = "LOCAL";

        $this->jobConfig = (object) [
            'Domain' => null,
            'JobID' => null,
            'Name' => null,
            'PeriodType' => null,
            'PeriodValue' => null,
            'ScheduleTime' => null,
            'ExecuteDuration' => null,
            'TimeZone' => 'Asia/Bangkok',
            'AdditionCondition' => [
                'Success' => true
            ],
            'SkipCheck' => false,
            'Notification' => [
                'Line' => null,
                'sms' => null,
                'call' => null,
                'mail' => null,
            ],
            'NotiFrequency' => null,
            'ArchiveLogUnit' => null,
            'ArchiveLogValue' => null
        ];
        $this->jobExecuteInfo = (object) [
            'Success' => true,
            'Error' => null
        ];

        $this->jobConfigHasChange = (object) [
            'Domain' => false,
            'JobID' => false,
            'Name' => false,
            'PeriodType' => false,
            'PeriodValue' => false,
            'ScheduleTime' => false,
            'ExecuteDuration' => false,
            'TimeZone' => false,
            'AdditionCondition' => false,
            'SkipCheck' => false,
            'Notification' => false,
            'NotiFrequency' => false,
            'ArchiveLogUnit' => false,
            'ArchiveLogValue' => false
        ];
        $this->jobExecuteInfoHasChange = (object) [
            'Success' => false,
            'Error' => false
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
        return $this->jobConfigHasChange->Domain =
        (($this->jobConfig->Domain = $domain)? true : false);
    }
    
    public function setJobID($jobID)
    {
        return $this->jobConfigHasChange->JobID =
        (($this->jobConfig->JobID = $jobID)? true : false);
    }
    
    public function setName($name)
    {
        return $this->jobConfigHasChange->Name =
        (($this->jobConfig->Name = $name)? true : false);
    }
    
    public function setPeriodTypeMin()
    {
        return $this->jobConfigHasChange->PeriodType =
        (($this->jobConfig->PeriodType = 'min')? true : false);
    }
    
    public function setPeriodTypeDaily()
    {
        return $this->jobConfigHasChange->PeriodType =
        (($this->jobConfig->PeriodType = 'daily')? true : false);
    }
    
    public function setPeriodTypeDate()
    {
        return $this->jobConfigHasChange->PeriodType =
        (($this->jobConfig->PeriodValue = 'date')? true : false);
    }
    
    public function setPeriodTypeDateMonth()
    {
        return $this->jobConfigHasChange->PeriodType =
        (($this->jobConfig->PeriodValue = 'datemonth')? true : false);
    }
    
    public function setPeriodTypeOnce()
    {
        return $this->jobConfigHasChange->PeriodType =
        (($this->jobConfig->PeriodValue = 'once')? true : false);
    }
    
    public function setPeriodValue($value)
    {
        return $this->jobConfigHasChange->PeriodValue =
        (($this->jobConfig->PeriodValue = $value)? true : false);
    }
    
    public function setScheduleTime($scheduleTime)
    {
        return $this->jobConfigHasChange->ScheduleTime =
        (($this->jobConfig->ScheduleTime = $scheduleTime)? true : false);
    }
    
    public function setExecuteDuration($executeDuration)
    {
        return $this->jobConfigHasChange->ExecuteDuration =
        (($this->jobConfig->ExecuteDuration = $executeDuration)? true : false);
    }
    
    public function setTimeZone($timeZone)
    {
        return $this->jobConfigHasChange->TimeZone =
        (($this->jobConfig->TimeZone = $timeZone)? true : false);
    }
    
    public function setAdditionCondition($success)
    {
        return $this->jobConfigHasChange->AdditionCondition =
        (($this->jobConfig->AdditionCondition['Success'] = $success)? true : false);
    }
    
    public function setSkipCheck($skip)
    {
        return $this->jobConfigHasChange->SkipCheck =
        (($this->jobConfig->SkipCheck = $skip)? true : false);
    }
    
    public function setLINENotification($lineToken)
    {
        return $this->jobConfigHasChange->Notification =
        (($this->jobConfig->Notification['Line'] = $lineToken)? true : false);
    }
    
    public function setSMSNotification($phoneNumber)
    {
        return $this->jobConfigHasChange->Notification = 
        (($this->jobConfig->Notification['sms'] = $phoneNumber)? true : false);
    }
    
    public function setPhoneNotification($phoneNumber)
    {
        return $this->jobConfigHasChange->Notification =
        (($this->jobConfig->Notification['call'] = $phoneNumber)? true : false);
    }
    
    public function setMailNotification($mail)
    {
        return $this->jobConfigHasChange->Notification =
        (($this->jobConfig->Notification['mail'] = $mail)? true : false);
    }
    
    public function setNotiFrequency($frequency)
    {
        return $this->jobConfigHasChange->NotiFrequency =
        (($this->jobConfig->NotiFrequency = $frequency)? true : false);
    }
    
    public function setArchiveLogUnitDay()
    {
        return $this->jobConfigHasChange->ArchiveLogUnit =
        (($this->jobConfig->ArchiveLogUnit = 'D')? true : false);
    }
    
    public function setArchiveLogUnitMonth()
    {
        return $this->jobConfigHasChange->ArchiveLogUnit =
        (($this->jobConfig->ArchiveLogUnit = 'M')? true : false);
    }
    
    public function setArchiveLogValue($value)
    {
        return $this->jobConfigHasChange->ArchiveLogValue =
        (($this->jobConfig->ArchiveLogValue = $value)? true : false);
    }
    
    public function setSuccess($success)
    {
        return $this->jobExecuteInfoHasChange->Success =
        (($this->jobExecuteInfo->Success = $success)? true : false);
    }
    
    public function setError($message)
    {
        return $this->jobExecuteInfoHasChange->Error =
        (($this->jobExecuteInfo->Error = $message)? true : false);
    }

    public function getJobActiveStatus()
    {
        $domain = $this->jobConfig->Domain;
        $jobID = $this->jobConfig->JobID;

        try {
            if ($domain == null)
            {
                throw new ErrorException('Domain is null.');
            }
            elseif($jobID == null)
            {
                throw new ErrorException('JobID is null.');
            }
        } catch (ErrorException $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            return;
        }

        $config = new \stdClass();
        $config->JobConfig = $this->jobConfig;
        $config->JobExecuteInfo = $this->jobExecuteInfo;
        $config = json_encode($config);

        return JobAPI::getJobActiveStatus($domain, $jobID, $config, $this->env);
    }

    public function updateJobStatus($errorMessage = '')
    {
        try {
            $this->checkData();
        } catch (ErrorException $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            return;
        }

        if ($errorMessage != '')
        {
            $this->setSuccess(false);
            $this->setError($errorMessage);
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

        try {
            if ($domain == null)
            {
                throw new ErrorException('Domain is null.');
            }
            elseif($jobID == null)
            {
                throw new ErrorException('JobID is null.');
            }
        } catch (ErrorException $e) {
            echo 'Error: ' . $e->getMessage() . "\n";
            return;
        }
        
        JobAPI::updateJobRunningStatus($domain, $jobID, $this->env);
    }

    public function checkData()
    {
        if ($this->jobConfig->Domain == null) {
            throw new ErrorException('property Domain need to assigned.');
        }
        if ($this->jobConfig->JobID == null) {
            throw new ErrorException('property JobID need to assigned.');
        }
        if ($this->jobConfig->Name == null) {
            throw new ErrorException('property Name need to assigned.');
        }
        if ($this->jobConfig->PeriodType == null) {
            throw new ErrorException('property PeriodType need to assigned.');
        }
        if ($this->jobConfig->PeriodValue == null) {
            throw new ErrorException('property PeriodValue need to assigned.');
        }
        if ($this->jobConfig->ScheduleTime == null) {
            throw new ErrorException('error property ScheduleTime need to assigned.');
        }
        if ($this->jobConfig->ExecuteDuration == null) {
            throw new ErrorException('error property ExecuteDuration need to assigned.');
        }
        if ($this->jobConfig->TimeZone == null) {
            throw new ErrorException('error property TimeZone need to assigned.');
        }
        if ($this->jobConfigHasChange->Notification == false) {
            throw new ErrorException('error property Notification need to assigned.');
        }
        if ($this->jobConfig->NotiFrequency == null) {
            throw new ErrorException('error property NotiFrequency need to assigned.');
        }
        if ($this->jobConfig->ArchiveLogUnit == null) {
            throw new ErrorException('error property ArchiveLogUnit need to assigned.');
        }
        if ($this->jobConfig->ArchiveLogValue == null) {
            throw new ErrorException('error property ArchiveLogValue need to assigned.');
        }
        return true;
    }
    
    public function updateJobDashboard($value, $Matric='Monitor', $DimensionName='default'){
        $namespace = $this->jobConfig->Domain.':'.$this->jobConfig->JobID;
        $date = new \DateTime();
        $metricData = [
            [
                'MetricName' => $Matric,
                'Timestamp' => $date->getTimestamp(), 
                'Dimensions' => [
                    [
                        'Name' => $Matric,
                        'Value' => $DimensionName
                        
                    ]
                ],
                'Unit' => 'Count',
                'Value' => $value
            ]
        ];

        $cloudWatchRegion = 'ap-southeast-1';
        $cloudWatchClient = new CloudWatchClient([
            'profile' => 'default',
            'region' => $cloudWatchRegion,
            'version' => '2010-08-01'
        ]);

        return $this->putMetricData($cloudWatchClient, $cloudWatchRegion, $namespace, $metricData); 
    }

    private function putMetricData($cloudWatchClient, $cloudWatchRegion, $namespace, $metricData)
    {
        try {
            $result = $cloudWatchClient->putMetricData([
                'Namespace' => $namespace,
                'MetricData' => $metricData
            ]);
            
            if (isset($result['@metadata']['effectiveUri']))
            {
                if ($result['@metadata']['effectiveUri'] == 
                    'https://monitoring.' . $cloudWatchRegion . '.amazonaws.com')
                {
                    return 'Successfully published datapoint(s).';
                } else {
                    return 'Could not publish datapoint(s).';
                }
            } else {
                return 'Error: Could not publish datapoint(s).';
            }
        } catch (AwsException $e) {
            return 'Error: ' . $e->getAwsErrorMessage();
        }
    }
}