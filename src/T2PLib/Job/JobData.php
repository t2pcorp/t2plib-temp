<?php

namespace T2PLib\Job;

use T2PLib\Models\Job;
use Carbon\Carbon;

class JobData
{
    protected $domain;
    protected $jobID;
    protected $lastRun;
    protected $now;
    protected $periodType;
    protected $periods;
    protected $scheduleTime;
    protected $execute;
    protected $timeZone;
    protected $skipCheck;
    protected $notification;
    protected $notiFrequency;

    public function initial(Job $job)
    {
        $jobModifyData = new JobModifyData();
        $this->domain = $job->domain;
        $this->jobID = $job->job_id;
        $this->now = Carbon::now($job->time_zone);
        $this->lastRun = Carbon::parse($job->last_run, $job->time_zone);
        $this->periodType = $job->period_type;
        $this->periods = $jobModifyData->modifyPeriods($job->period_type, $jobModifyData->getArray($job->period_value));
        $this->scheduleTime = $job->schedule_time;
        $this->execute = $job->execute_duration;
        $this->timeZone = $job->time_zone;
        $this->skipCheck = $job->skip_check;
        $this->notification = $job->notification;
        $this->notiFrequency = $job->noti_frequency;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getJobID()
    {
        return $this->jobID;
    }

    public function getLastRun()
    {
        return $this->lastRun;
    }
    
    public function getNow()
    {
        return $this->now;
    }
    
    public function getPeriodType()
    {
        return $this->periodType;
    }
    
    public function getPeriod()
    {
        return $this->periods[0];
    }
    
    public function getPeriods()
    {
        return $this->periods;
    }
    
    public function getScheduleTime()
    {
        return $this->scheduleTime;
    }
    
    public function getTimeZone()
    {
        return $this->timeZone;
    }
    
    public function getExecute()
    {
        return $this->execute;
    }
    
    public function getSkipCheck()
    {
        return $this->skipCheck;
    }
    
    public function getNotification()
    {
        return $this->notification;
    }
    
    public function getNotiFrequency()
    {
        return $this->notiFrequency;
    }

    public function setDomain($domain)
    {
        return $this->domain = $domain;
    }

    public function setJobID($jobID)
    {
        return $this->jobID = $jobID;
    }

    public function setLastRun($lastRun)
    {
        return $this->lastRun = $lastRun;
    }
    
    public function setNow($now)
    {
        return $this->now = $now;
    }
    
    public function setPeriodType($periodType)
    {
        return $this->periodType = $periodType;
    }
    
    public function setPeriods($periods)
    {
        return $this->periods = $periods;
    }
    
    public function setScheduleTime($scheduleTime)
    {
        return $this->scheduleTime = $scheduleTime;
    }
    
    public function setTimeZone($timeZone)
    {
        return $this->timeZone = $timeZone;
    }
    
    public function setExecute($execute)
    {
        return $this->execute = $execute;
    }
    
    public function setSkipCheck($skipCheck)
    {
        return $this->skipCheck = $skipCheck;
    }
    
    public function setNotification($notification)
    {
        return $this->notification = $notification;
    }
    
    public function setNotiFrequency($notiFrequency)
    {
        return $this->notiFrequency = $notiFrequency;
    }
}