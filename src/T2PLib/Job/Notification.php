<?php

namespace T2PLib\Job;

include_once('/data/_inc/___cmd_inc.php');

class Notification
{
    protected $jobData;
    protected $prevRun;
    protected $frequency;
    protected $lastSuccess;
    protected $lastUpdate;
    protected $errorMessage;

    public function __construct($jobData, $prevRun, $lastSuccess, $lastUpdate, $errorMessage)
    {
        $this->jobData = $jobData;
        $this->prevRun = $prevRun;
        $this->frequency = intval($this->jobData->getNotiFrequency());
        $this->lastSuccess = $lastSuccess;
        $this->lastUpdate = $lastUpdate;
        $this->errorMessage = $errorMessage;
        $this->callFunction();
    }

    public function callFunction()
    {
        $this->findNotifications();
    }

    public function findNotifications()
    {
        foreach($this->jobData->getNotification() as $type=>$address)
        {
            $this->sendNotify($type, $address);
        }
    }

    public function sendNotify($type, $address)
    {
        $msgToNotify = $this->message();
        if ($this->jobData->getSkipCheck() == false)
        {
            switch($type)
            {
                case 'Line':
                    \T2P\Util\Util::sendNotiLineWithPeriod($this->jobData->getJobID() . ':' . $address,
                     $msgToNotify, true, $hours=0, $minutes=0, $seconds=1);
                    break;
                case 'sms':
                    //call sms
                    break;
                case 'call':
                    //call phone
                    break;
                case 'mail':
                    //call mail
                    break;
                default:
                    break;
            }
        }
    }

    public function message()
    {
        $date = $this->prevRun;
        $maxTime = $this->prevRun->copy()
                ->addMinutes($this->jobData->getExecute())->toTimeString();
        $text = [
            $this->jobData->getDomain(),
            $this->jobData->getJobID(),
            'Status:'.'fail',
            'Should run: '.$date.' - '.$maxTime,
            'Last update: '.$this->lastUpdate,
            'Last success: '.$this->lastSuccess,
            'Error message: ' . $this->errorMessage
        ];
        $msgToNotify = json_encode($text, JSON_PRETTY_PRINT);
        return $msgToNotify;
    }
}