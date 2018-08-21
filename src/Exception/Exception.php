<?php namespace Ovide\Phest\Exception;

use Ovide\Phest\Response;

/**
 * 
 * @author Albert Ovide <albert@ovide.net>
 */
abstract class Exception extends \Exception
{
    protected static $_code = 500;

    public function __construct($message = null, $code = null, $previous = null)
    {
        if ($code === null) {
            $code = static::$_code;
        }

        if ($message === null) {
            $message = Response::$status[$code] ?? '';
        }
        parent::__construct($message, $code, $previous);
    }
}
