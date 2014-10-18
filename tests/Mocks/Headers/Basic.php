<?php namespace Mocks\Headers;

use Ovide\Libs\Mvc\Rest;

class Basic extends Rest\Header\Handler
{
    const HEADER = 'FOO';
    
    public static $_handleCalled = 0;
    
    public static $_initCalled = 0;
    
    public function init()
    {
        parent::init();
        self::$_initCalled++;
    }
    
    public function before()
    {
        self::$_handleCalled++;
    }

    public function after() {
        
    }

    public function finish() {
        
    }

}
