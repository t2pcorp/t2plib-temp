<?php

namespace T2PLib\Job;

class JobModifyData
{
    public function modifyPeriods($periodType, $periods)
    {
        $modify = array();
        foreach ($periods as $period)
        {
            $period = $this->checkPeriodType($periodType, $period);
            array_push($modify, $period);
        }
        sort($modify);
        return $modify;
    }

    public function checkPeriodType($periodType, $period)
    {
        switch($periodType)
        {
            case 'date':
                $period = ($period == "L"? "31": $period);
                return $period;
            default:
                return $period;
        }
    }
    
    public function getArray($list)
    {
        return array_map('trim', explode(',', $list));
    }
}