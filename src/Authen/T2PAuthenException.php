<?php

namespace \T2PAuthen;

/**
 * Arthor: Amnat
 * Create Date: 2018/03/14
 *
 * Modify History:
 * 1.init file Date: 2018/03/14
 *
 * Short Description:
 *  Provide Exception Message instead default message
 *
 *
 * PHP version: 5.6
 *  */

class T2PAuthenException extends \Exception
{
    private $previous;
    /**
     * // Redefine the exception so message
     *
     * @param      <type>      $message   The message
     * @param      integer     $code      The code
     * @param      \Exception  $previous  The previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
       
        if (!is_null($previous)) {
            $this->previous = $previous;
        }
    }
    /**
     * custom string representation of object
     *
     * @return     string  String representation of the object.
     */

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
