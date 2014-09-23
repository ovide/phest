<?php namespace Ovide\Libs\Mvc\Rest\Exception;

use Ovide\Libs\Mvc\Rest\Controller as Rest;

class NotFound extends Exception
{
    protected static $_code = Rest::NOT_FOUND;
}
