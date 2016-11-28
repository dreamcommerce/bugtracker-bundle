<?php

namespace DreamCommerce\Component\BugTracker\Exception;

class NotDefinedException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        if(empty($message)) {
            $message = 'Variable has been not defined';
        } else {
            $message = 'Variable "' . $message . '" has been not defined';
        }

        parent::__construct($message, $code, $previous);
    }
}