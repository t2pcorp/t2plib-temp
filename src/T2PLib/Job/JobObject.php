<?php

namespace T2PLib\Job;

use T2PLib\Job\JobData;
use T2PLib\Job\JobSchedule;
use T2PLib\Job\JobStatus;
use T2PLib\Job\Notification;

class JobObject
{
    protected $jobData;
    protected $schedule;
    protected $status;

    public function __construct($job)
    {
        $this->jobData = new JobData();
        $this->jobData->initial($job);
        $this->schedule = new JobSchedule($this->jobData);
        $this->status = new JobStatus($this->schedule);
    }

    public function getJobData()
    {
        return $this->jobData;
    }

    public function getSchedule()
    {
        return $this->schedule;
    }

    public function getStatus()
    {
        return $this->status->getStatus();
    }
}