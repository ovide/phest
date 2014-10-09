<?php namespace Ovide\Libs\Mvc\Rest\Exception;

use Ovide\Libs\Mvc\Rest\Response;

/**
 * The method specified in the Request-Line is not allowed for
 * the resource identified by the Request-URI.
 * The response MUST include an Allow header containing a list of valid methods
 * for the requested resource.
 *
 * A request was made of a resource using a request method not supported
 * by that resource; for example, using GET on a form which requires data
 * to be presented via POST, or using PUT on a read-only resource.
 */
class MethodNotAllowed extends Exception
{
    protected static $_code = Response::NOT_ALLOWED;
}
