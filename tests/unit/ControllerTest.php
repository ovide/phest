<?php namespace Ovide\Libs\Mvc\Rest;

require_once __DIR__.'/../mocks/BasicMock.php';
require_once __DIR__.'/../mocks/FooMock.php';

use Codeception\Util\Stub;

class ControllerTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;
    
    protected function _before()
    {
        //$_SERVER['REQUEST_URI'] = 'logs';
        //$_GET["_url"] = '/logs';
    }

    protected function _after()
    {
    }

    // tests
    public function testIndexGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'FOO_METHOD';
        $I = $this->tester;
        
        $invoked = false;
        $controller = Stub::make(new \BasicMock, [
            '_call' => Stub::once(function ($method, $id) use(&$invoked) {
                $invoked = true;
                $this->tester->assertEquals('FOO_METHOD', $method);
                $this->tester->assertEquals('var_id', $id);
            })
        ]);
        $resp = $controller->_index('var_id');
        $I->assertTrue($invoked);
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
    }
    
    public function testIndexWithException()
    {
        $_SERVER['REQUEST_METHOD'] = 'FOO_METHOD';
        $I = $this->tester;
        
        $invoked = false;
        $controller = Stub::make(new \BasicMock, [
            '_call'     => Stub::once(
                function ($method, $id) use(&$invoked) {
                    $invoked = true;
                    throw new \Exception();
                }
            )
        ]);
        $resp = $controller->_index('var_id');
        $I->assertTrue($invoked);
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
    }
    
    public function testCall()
    {
        $I = $this->tester;
       
        $rsp = new \Phalcon\Http\Response();
        Controller::notFound($rsp);
        $h = $rsp->getHeaders();
        $I->assertEquals('404 Not Found', $h->get('Status'));
    }
}