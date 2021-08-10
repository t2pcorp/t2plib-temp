<?php

namespace T2PLib\Job;

use Carbon\Carbon;

class JobStatus
{
    protected $jobScheduleTime;
    protected $status;
    protected $jobData;

    public function __construct($jobScheduleTime)
    {
        $this->jobScheduleTime = $jobScheduleTime;
        $this->jobData = $this->jobScheduleTime->getJobData();
        $this->status = 'idle';
        $this->callFunction();
    }

    public function callFunction()
    {
        $this->checkJobStatus();
    }

    public function checkJobStatus()
    {
        if ($this->jobData->getPeriodType() == 'once')
        {
            $this->status = $this->jobData->getNow()->copy()
                ->between($this->jobScheduleTime->getPreviousRun(), 
                $this->jobScheduleTime->getPreviousRun()->copy()->addMinutes($this->jobData->getExecute()));
        }
        else
        {
            $this->status = $this->jobData->getLastRun()->copy()
                ->between($this->jobScheduleTime->getPreviousRun(), 
                $this->jobScheduleTime->getPreviousRun()->copy()->addMinutes($this->jobData->getExecute()));
        }
        $this->status = $this->status? 'success' : 'fail';
    }
    
    public function getStatus()
    {
        return $this->status;
    }

    public function getJobData()
    {
        return $this->jobData;
    }

    public function getJobScheduleTime()
    {
        return $this->jobScheduleTime;
    }

    public function setStatus($status)
    {
        return $this->status = $status;
    }
}