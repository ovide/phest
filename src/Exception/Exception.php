<?php namespace Ovide\Libs\Mvc\Rest\Exception;

use Ovide\Libs\Mvc\Rest\Response;

abstract class Exception extends \Exception
{
    protected static $_code = 500;
    protected static $_message;

    public function __construct($message = null, $code = null, $previous = null)
    {
        if ($code === null) {
            $code = static::$_code;
        }

        if ($message === null) {
            $message = static::$_message ?
                static::$_message :
                Response::$status[$code];
        }
        parent::__construct($message, $code, $previous);
    }
}
