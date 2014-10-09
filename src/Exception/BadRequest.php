<?php namespace Ovide\Libs\Mvc\Rest\Exception;

use Ovide\Libs\Mvc\Rest\Response;

/**
 * The request could not be understood by the server due to malformed syntax.
 * The client SHOULD NOT repeat the request without modifications.
 *
 * The request cannot be fulfilled due to bad syntax.
 *
 * General error when fulfilling the request would cause an invalid state.
 * Domain validation errors, missing data, etc. are some examples.
 */
class BadRequest extends Exception
{
    protected static $_code = Response::BAD_REQUEST;
}
