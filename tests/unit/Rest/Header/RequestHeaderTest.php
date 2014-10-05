<?php

class RequestHeaderTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testThrowException()
    {
        $I = $this->tester;
        
        $request  = new \Phalcon\Http\Request();
        $header = new \Mocks\Headers\InvalidRequestHeader($request);
        
        try {
            
            $header->init();
            $I->assertTrue(false);
        } catch (\LogicException $ex) {
            $I->assertTrue(true);
        }
    }
    
    public function testGet()
    {
        $I = $this->tester;
        
        $_SERVER['FOO'] = 'var';
        
        $request  = new \Phalcon\Http\Request();
        
        $header = new Mocks\Headers\Basic($request);
        $header->init();
        $I->assertEquals('var', $header->get());
    }

}