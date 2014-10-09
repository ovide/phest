<?php namespace Ovide\Libs\Mvc\Rest\Exception;

use Ovide\Libs\Mvc\Rest\Response;

/**
 * The server understood the request, but is refusing to fulfill it.
 * Authorization will not help and the request SHOULD NOT be repeated.
 *
 * If the request method was not HEAD and the server wishes to make public
 * why the request has not been fulfilled,
 * it SHOULD describe the reason for the refusal in the entity.
 *
 * If the server does not wish to make this information available to the client,
 * the status code 404 (Not Found) can be used instead.
 *
 * The request was a legal request, but the server is refusing to respond to it.
 * Unlike a 401 Unauthorized response, authenticating will make no difference.
 *
 * Error code for user not authorized to perform the operation or
 * the resource is unavailable for some reason (e.g. time constraints, etc.).
 */
class Forbidden extends Exception
{
    protected static $_code = Response::FORBIDDEN;
}
