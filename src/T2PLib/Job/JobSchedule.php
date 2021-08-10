<?php

namespace T2PLib\Job;

use Carbon\Carbon;

class JobSchedule
{
    protected $jobData;
    protected $jobPrevRun;
    protected $periods;

    public function __construct($jobData)
    {
        $this->jobPrevRun = "0000-00-00 00:00:00";
        $this->periods = $jobData->getPeriods();
        $this->jobData = $jobData;
        $this->callFunction();
    }

    public function callFunction()
    {
        $this->findPreviousRun();
    }

    public function findPreviousRunWithPeriod($period)
    {
        $now = $this->jobData->getNow();
        $scheduleTime = $this->jobData->getScheduleTime();
        $scheduleRun = $now->copy()->setTimeFrom($scheduleTime);
        $timeZone = $this->jobData->getTimeZone();
        switch ($this->jobData->getPeriodType())
        {
            case 'min':
                $now = $this->jobData->getNow();
                $lastRun = $this->jobData->getLastRun();
                if ($now->diffInMinutes($lastRun) > ($this->jobData->getPeriod() + $this->jobData->getExecute()))
                {
                    $jobScheduleRun = $now->copy()->sub(intval($period), $this->jobData->getPeriodType())
                            ->sub(intval($this->jobData->getExecute()), $this->jobData->getPeriodType());
                }
                else
                {
                    $jobScheduleRun = $lastRun->copy();
                }
                return $jobScheduleRun;
            case 'daily':
                if ($now->dayOfWeek == $period)
                {
                    if ($now->lessThan($scheduleRun))
                    {
                        $key = array_search($period, $this->periods);
                        if ($key != 0)
                        {
                            $jobScheduleRun = $now->copy()->previous(intval($this->periods[$key-1]))
                                    ->setTimeFrom($scheduleTime);
                        }
                        else
                        {
                            $jobScheduleRun = $now->copy()->previous(intval($period))
                                    ->setTimeFrom($scheduleTime);
                        }
                    }
                    else
                    {
                        $jobScheduleRun = $now->copy()->setTimeFrom($scheduleTime);
                    }
                }
                else
                {
                    $jobScheduleRun = $now->copy()->previous(intval($period))
                            ->setTimeFrom($scheduleTime);
                }
                return $jobScheduleRun;
            case 'date':
                if ($now->day == $period)
                {
                    if ($now->lessThan($scheduleRun))
                    {
                        $key = array_search($period, $this->periods);
                        if ($key != 0)
                        {
                            $jobScheduleRun = $now->copy()->setUnitNoOverflow('day', intval($this->periods[$key-1]), 'month')
                                    ->setTimeFrom($scheduleTime);
                        }
                        else
                        {
                            $jobScheduleRun = $now->copy()->subMonth()->setUnitNoOverflow('day', intval($period), 'month')
                                    ->setTimeFrom($scheduleTime);
                        }
                    }
                    else
                    {
                        $jobScheduleRun = $now->copy()->setTimeFrom($scheduleTime);
                    }
                }
                else
                {
                    $scheduleRun = $now->copy()->setUnitNoOverflow('day', intval($period), 'month')
                            ->setTimeFrom($scheduleTime);
                    if ($now->lessThan($scheduleRun))
                    {
                        $jobScheduleRun = $now->copy()->subMonth()->setUnitNoOverflow('day', intval($period), 'month')
                                ->setTimeFrom($scheduleTime);
                    }
                    else
                    {
                        $jobScheduleRun = $now->copy()->setUnitNoOverflow('day', intval($period), 'month')
                                ->setTimeFrom($scheduleTime);
                    }
                }
                return $jobScheduleRun;
            case 'datemonth':
                list($month, $day) = explode(':', $period);
                if ($now->month == $month && $now->day == $day)
                {
                    if ($now->lessThan($scheduleRun))
                    {
                        $key = array_search($period, $this->periods);
                        if ($key != 0)
                        {
                            list($month, $day) = explode(':', $this->periods[$key-1]);
                            $jobScheduleRun = $now->copy()->setUnitNoOverflow('month', intval($month), 'year')
                                    ->setUnitNoOverflow('day', intval($day), 'month')
                                    ->setTimeFrom($scheduleTime);
                        }
                        else
                        {
                            $jobScheduleRun = $now->copy()->subYear()->setUnitNoOverflow('month', intval($month), 'year')
                                    ->setUnitNoOverflow('day', intval($day), 'month')
                                    ->setTimeFrom($scheduleTime);
                        }
                    }
                    else
                    {
                        $jobScheduleRun = $now->copy()->setTimeFrom($scheduleTime);
                    }
                }
                else
                {
                    $scheduleRun = $now->copy()->setUnitNoOverflow('month', intval($month), 'year')
                            ->setUnitNoOverflow('day', intval($day), 'month')
                            ->setTimeFrom($scheduleTime);
                    if ($now->lessThan($scheduleRun))
                    {
                        $jobScheduleRun = $now->copy()->subYear()->setUnitNoOverflow('month', intval($month), 'year')
                                ->setUnitNoOverflow('day', intval($day), 'month')
                                ->setTimeFrom($scheduleTime);
                    }
                    else
                    {
                        $jobScheduleRun = $now->copy()->setUnitNoOverflow('month', intval($month), 'year')
                                ->setUnitNoOverflow('day', intval($day), 'month')
                                ->setTimeFrom($scheduleTime);
                    }
                }
                return $jobScheduleRun;
            case 'once':
                $jobScheduleRun = Carbon::parse($period . " " . $scheduleTime, $timeZone);
                return $jobScheduleRun;
        }
    }

    public function findPreviousRun()
    {
        if (is_array($this->jobData->getPeriods()))
        {
            foreach ($this->jobData->getPeriods() as $period)
            {
                $newDateTime = $this->findPreviousRunWithPeriod($period);
                $this->jobPrevRun = $this->jobPrevRun < $newDateTime? $newDateTime : $this->jobPrevRun;
            }
        }
        else
        {
            $newDateTime = $this->findPreviousRunWithPeriod($period);
            $this->jobPrevRun = $this->jobPrevRun < $newDateTime? $newDateTime : $this->jobPrevRun;
        }
    }

    public function getPreviousRun()
    {
        return $this->jobPrevRun;
    }

    public function getJobData()
    {
        return $this->jobData;
    }
}