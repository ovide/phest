<?php namespace Mocks\Headers;

use Ovide\Libs\Mvc\Rest;

class Basic extends Rest\Header\Handler
{
    const HEADER = 'FOO';
    
    public static $_called = 0;
    
    public function handle() {
        self::$_called++;
    }
}
