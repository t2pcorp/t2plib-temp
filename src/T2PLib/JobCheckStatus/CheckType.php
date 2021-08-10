<?php

namespace T2PLib\JobCheckStatus;

class CheckType
{
    public static function check($type)
    {
        switch($type)
        {
            case 'min':
                return true;
            case 'daily':
                return true;
            case 'date':
                return true;
            case 'datemonth':
                return true;
            case 'once':
                return true;
            default:
                return false;
        }
    }
}