<?php

namespace App\Job;

use Illuminate\Support\Facades\DB;
use App\Models\Job;
use Carbon\Carbon;
use App\Job\JobData;
use App\Job\JobSchedule;
use App\Job\JobStatus;
use App\Job\Notification;
use App\JobClass\JobClass;

include 'vendor/autoload.php';
$job = new JobClass();
$job->JobConfig->Domain = 'TEST';
$job->JobConfig->JobID = 'test';

$job->JobConfig->Name = 'Job Name';
$job->JobConfig->PeriodType = 'min';
$job->JobConfig->PeriodValue = '3';

$job->JobConfig->ScheduleTime = '00:01:00';
$job->JobConfig->ExecuteDuration = '60';
$job->JobConfig->TimeZone = 'Asia/Bangkok';

$job->JobConfig->AdditionCondition = ['Step' => 9];
$job->JobConfig->SkipCheck = false;
$job->JobConfig->Notification = ['Line' => 'notitoken', 'sms' => '0811111111'];

$job->JobConfig->NotiFrequency = '1';
$job->JobConfig->ArchiveLogUnit = 'D';
$job->JobConfig->ArchiveLogValue = '1';

$job->JobExecuteInfo->ExecuteTime = '2021-06-08 01:00:01';
$job->JobExecuteInfo->Steps = 7;
$job->JobExecuteInfo->Error = "";
$job->updateJobStatus($job);



// ------------------------------------------------------------------------
// $timeZone = 'Asia/Bangkok';
// $now = Carbon::now($timeZone);
// $lastRun = Carbon::parse("2021-08-02 11:00:00", $timeZone);
// ($lastRun)->subDays(10);
// echo ($lastRun) . "\n";
// $job = new Job;
// $job->domain = "domain";
// $job->job_id = "id";
// $job->last_run = "2021-08-02";
// $job->name = "test test test";
// $job->period_type = "once";
// $job->period_value = "2021-08-02";
// $job->schedule_time = "15:00:00";
// $job->execute_duration = "1";
// $job->time_zone = "Asia/Bangkok";
// $job->skip_check = false;
// $job->noti_frequency = "1";
// $job->archive_log_unit = "D";
// $job->archive_log_value = "10";

// $period = $job->period_value;
// $scheduleTime = $job->schedule_time;
// $lastRun = Carbon::parse($job->last_run, $timeZone);
// $jobScheduleRun = Carbon::parse($period . " " . $scheduleTime, $timeZone);
// $lastRun->setDateTimeFrom($job->last_run);

// $jobConfig = new JobConfig();
// $jobConfig->initial($job);
// $schedule = new JobSchedule($jobConfig);
// echo $schedule->getPreviousRun() . "\n";

// $lastRun = '2014-02-06 16:34:00';
// $lastRun = $lastRun? Carbon::parse($lastRun, $timeZone) : $now;

// $lastRun->setTimezone('UTC');
// echo $now;

//test archive
// $now = Carbon::now()->setDateTimeFrom(new \DateTime("2021-07-15 02:04:00"));
// $lastRun = $now->copy()->setDateTimeFrom(new \DateTime("2021-07-15 23:04:00"));
// echo $now->diffForHumans($lastRun, ['syntax' => CarbonInterface::DIFF_ABSOLUTE]) . "\n";
// echo $now->diffForHumans($lastRun, ['syntax' => CarbonInterface::DIFF_ABSOLUTE]) >= "1 hour";
// echo "\n";
// echo $now . "\n";
// echo $lastRun . "\n";
// echo $now->hour == $lastRun->hour? "true\n":"false\n";

//test minute

// $period = "1";
// $execute = 1;
// $scheduleTime = "23:00:00";
// $periodType = "min";
// $now = Carbon::now()->setDateTimeFrom(new \DateTime("2021-07-15 23:08:00"));
// $lastRun = $now->copy()->setDateTimeFrom(new \DateTime("2021-07-15 23:04:00"));

// $jobConfig = new JobConfig();
// $jobConfig->initial($lastRun, $now, $scheduleTime, $period, $execute, $periodType);
// $notification = array("line:notitoken", "sms:0123456789", "call:9876543210", "mail:aaa@bbb.com");
// $jobConfig->setNotification($notification);

// $jobSchedule = new JobSchedule($jobConfig);

// $jobStatus = new JobStatus($jobSchedule);
// $noti = new Notification($jobStatus);

// echo "job status = " . $jobStatus->getStatus() . "\n";
// echo "-------------------\n";

//--------------------------------------------------------------
//test daily
// $period = "0,1,2,4,5,6";  // 0 sunday
// $periods = array_map('trim', explode(',', $period));

// $execute = 15;
// $scheduleTime = "23:00:00";
// $periodType = "daily";
// $now = Carbon::now()->setDateTimeFrom(new \DateTime("2021-07-08 23:00:00"));
// $lastRun = $now->copy()->setDateTimeFrom(new \DateTime("2021-07-06 23:00:00")); //success

// $jobConfig = new JobConfig();
// $jobConfig->initialAllData($lastRun, $now, $scheduleTime, $periods, $execute, $periodType);

// $jobSchedule = new JobSchedule($jobConfig);

// $jobStatus = new JobStatus($jobSchedule);

// echo "job status = " . $jobStatus->getStatus() . "\n";
// echo "-------------------\n";

//--------------------------------------------------------------
// test date
// $period = "1 ,2, 3, 4, L";
// $periods = array_map('trim', explode(',', $period));

// $execute = 5;
// $scheduleTime = "23:00:00";
// $periodType = "date";
// $now = Carbon::now()->setDateTimeFrom(new \DateTime("2021-03-31 23:00:00"));
// $lastRun = $now->copy()->setDateTimeFrom(new \DateTime("2021-03-04 23:00:00"));

// $jobConfig = new JobConfig();
// $jobConfig->initialAllData($lastRun, $now, $scheduleTime, $periods, $execute, $periodType);
// $notification = array("line:notitoken", "sms:0123456789", "call:9876543210", "mail:aaa@bbb.com");
// $jobConfig->setNotification($notification);

// $jobSchedule = new JobSchedule($jobConfig);

// $jobStatus = new JobStatus($jobSchedule);
// $noti = new Notification($jobStatus);

// echo "job status = " . $jobStatus->getStatus() . "\n";
// echo "-------------------------------------\n";


//--------------------------------------------------------------
// test datemonth
// $period = "5:31, 6:20";
// $periods = array_map('trim', explode(',', $period));

// $execute = 15;
// $scheduleTime = "23:00:00";
// $periodType = "datemonth";
// $now = Carbon::now()->setDateTimeFrom(new \DateTime("2021-07-20 22:05:00"));
// $lastRun = $now->copy()->setDateTimeFrom(new \DateTime("2021-06-31 23:00:00")); //success
// // // $lastRun = $now->copy()->setDateTimeFrom(new \DateTime("2021-06-31 22:00:00")); // fail

// $jobConfig = new JobConfig();
// $jobConfig->initialAllData($lastRun, $now, $scheduleTime, $periods, $execute, $periodType);

// $jobSchedule = new JobSchedule($jobConfig);

// $jobStatus = new JobStatus($jobSchedule);
// echo "job status = " . $jobStatus->getStatus() . "\n";
// echo $jobStatus->getStatus() . "\n";
// echo "-------------------\n";

//--------------------------------------------------------------
//test once
// $period = "2021-07-30";
// $execute = 15;
// $scheduleTime = "23:00:00";
// $periodType = "once";
// $now = Carbon::now()->setDateTimeFrom(new \DateTime("2021-07-30 23:00:00"));
// $lastRun = $now->copy()->setDateTimeFrom(new \DateTime("2021-07-30 23:00:00"));
// $periods = $now->copy()->setDateTimeFrom(new \DateTime($period . " " . $scheduleTime));

// $jobConfig = new JobConfig();
// $jobConfig->initialAllData($lastRun, $now, $scheduleTime, $periods, $execute, $periodType);

// $jobSchedule = new JobSchedule($jobConfig);

// $jobStatus = new JobStatus($jobSchedule);

// echo "job status = " . $jobStatus->getStatus() . "\n";
// echo "-------------------\n";
