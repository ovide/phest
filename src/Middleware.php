<?php namespace Ovide\Libs\Mvc\Rest;

/**
 * @method bool beforeHandleRoute(\Phalcon\Events\Event $evt, \Ovide\Libs\Mvc\Rest\App $app, $data)
 * The main method is just called, at this point the application doesn’t know if there is some matched route
 * @method bool beforeExecuteRoute(\Phalcon\Events\Event $evt, \Ovide\Libs\Mvc\Rest\App $app, $data)
 * A route has been matched and it contains a valid handler, at this point the handler has not been executed
 * @method bool afterExecuteRoute(\Phalcon\Events\Event $evt, \Ovide\Libs\Mvc\Rest\App $app, $data)
 * Triggered after running the handler
 * @method bool beforeNotFound(\Phalcon\Events\Event $evt, \Ovide\Libs\Mvc\Rest\App $app, $data)
 * Triggered when any of the defined routes match the requested URI
 * @method bool afterHandleRoute(\Phalcon\Events\Event $evt, \Ovide\Libs\Mvc\Rest\App $app, $data)
 * Triggered after completing the whole process in a successful way
 */
abstract class Middleware implements \Phalcon\Mvc\Micro\MiddlewareInterface
{
    const HEADER = '';

    protected function getHeader()
    {
        $text = 'HTTP_'.strtoupper(static::HEADER);
        $header = isset($_SERVER[$text]) ? $_SERVER[$text] : '';
        return $header;
    }
}
