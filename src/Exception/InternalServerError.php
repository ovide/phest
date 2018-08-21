<?php namespace Ovide\Phest\Exception;

use Ovide\Phest\Response;

/**
 * The server encountered an unexpected condition which prevented it
 * from fulfilling the request.
 *
 * A generic error message, given when no more specific message is suitable.
 *
 * The general catch-all error when the server-side throws an exception.
 */
class InternalServerError extends Exception
{
    protected static $_code = Response::INTERNAL_ERROR;
}
