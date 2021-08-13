<?php

namespace T2PLib\JobCheckStatus;

class StoreJob
{
    public static function store($JobConfig, $data)
    {
        $job = new \stdClass;
        $job->domain = $JobConfig->Domain;
        $job->job_id = $JobConfig->JobID;
        $job->last_run = $data->last_jobUpdate;
        $job->period_type = $JobConfig->PeriodType;
        $job->period_value = $JobConfig->PeriodValue;
        $job->schedule_time = $JobConfig->ScheduleTime;
        $job->execute_duration = $JobConfig->ExecuteDuration;
        $job->time_zone = $JobConfig->TimeZone;
        $job->skip_check = $JobConfig->SkipCheck;
        $job->notification = $JobConfig->Notification;
        $job->noti_frequency = $JobConfig->NotiFrequency;
        $job->archive_log_unit = $JobConfig->ArchiveLogUnit;
        $job->archive_log_value = $JobConfig->ArchiveLogValue;
        return $job;
    }
}