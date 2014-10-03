<?php

use Mockery as m;
use Ovide\Libs\Mvc\Rest;
use Mocks\Controllers;

class ControllerTest extends \Codeception\TestCase\Test
{
   /**
    * @var UnitTester
    */
    protected $tester;
    
    /**
     * @var Rest\Controller
     */
    protected $controller;
    
    protected function _before()
    {
        //$_SERVER['REQUEST_URI'] = 'logs';
        //$_GET["_url"] = '/logs';
    }

    protected function _after()
    {
        m::close();
        $_SERVER['REQUEST_METHOD'] = null;
    }

    
    public function testIndexGetOneNoArgs()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $controller = m::mock(Controllers\Basic::class.'[getOne]')
                ->shouldReceive('getOne')
                ->once()
                ->withArgs(['foo'])
                ->getMock()
        ;
        
        $resp = $controller->_index('foo');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertLessThan(300, $h[0],
            "$status: May didn't call the mock method"
        );
    }
    
    public function testIndexGetOneWithArgs()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $I = $this->tester;
        
        $controller = m::mock(Controllers\Basic::class.'[getOne]')
                ->shouldReceive('getOne')
                ->once()
                ->withArgs(['foovar', 'foo', 'var'])
                ->getMock()
        ;
        
        $resp = $controller->_index('foo', 'var', 'foovar');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertLessThan(300, $h[0],
            "$status: May didn't call the mock method"
        );
    }
    
    public function testIndexGetNoArgs()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $controller = m::mock(Controllers\Basic::class.'[get]')
                ->shouldReceive('get')
                ->once()
                //->withNoArgs()
                ->withArgs([])
                ->getMock()
        ;
        
        $resp = $controller->_index('');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        //$I->assertLessThan(300, $h[0],
        //    "$status: May didn't call the mock method"
        //);
    }
    
    public function testIndexGetWithArgs()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $controller = m::mock(Controllers\Basic::class.'[get]')
                ->shouldReceive('get')
                ->once()
                ->withArgs(['foo', 'var'])
                ->getMock()
        ;
        
        $resp = $controller->_index('foo', 'var', '');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertLessThan(300, $h[0],
            "$status: May didn't call the mock method"
        );
    }
    
    public function testIndexPostNoArgs()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $controller = m::mock(Controllers\Basic::class.'[post]')
                ->shouldReceive('post')
                ->once()
                ->withArgs([[]])
                ->getMock()
        ;
        
        $resp = $controller->_index('');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertLessThan(300, $h[0],
            "$status: May didn't call the mock method"
        );
    }
    
    public function testIndexPostWithArgs()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $controller = m::mock(Controllers\Basic::class.'[post]')
                ->shouldReceive('post')
                ->once()
                ->withArgs(['foo', 'var', []])
                ->getMock()
        ;
        
        $resp = $controller->_index('foo', 'var', '');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertLessThan(300, $h[0],
            "$status: May didn't call the mock method"
        );
    }
    
    public function testIndexPutNoArgs()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        
        $controller = m::mock(Controllers\Basic::class.'[put]')
                ->shouldReceive('put')
                ->once()
                ->withArgs(['foo',[]])
                ->getMock()
        ;
        
        $resp = $controller->_index('foo');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertLessThan(300, $h[0],
            "$status: May didn't call the mock method"
        );
    }
    
    public function testIndexPutWithArgs()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        
        $controller = m::mock(Controllers\Basic::class.'[put]')
                ->shouldReceive('put')
                ->once()
                ->withArgs(['foo', 'var', 'foovar', []])
                ->getMock()
        ;
        
        $resp = $controller->_index('foo', 'var', 'foovar');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertLessThan(300, $h[0],
            "$status: May didn't call the mock method"
        );
    }
    
    public function testIndexDeleteNoArgs()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        
        $controller = m::mock(Controllers\Basic::class.'[delete]')
                ->shouldReceive('delete')
                ->once()
                ->withArgs(['foo'])
                ->getMock()
        ;
        
        $resp = $controller->_index('foo');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertLessThan(300, $h[0],
            "$status: May didn't call the mock method"
        );        
    }
    
    public function testIndexDeleteWithArgs()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        
        $controller = m::mock(Controllers\Basic::class.'[delete]')
                ->shouldReceive('delete')
                ->once()
                ->withArgs(['foovar', 'foo', 'var'])
                ->getMock()
        ;
        
        $resp = $controller->_index('foo','var','foovar');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertLessThan(300, $h[0],
            "$status: May didn't call the mock method"
        );        
    }
    
    public function testIndexGetNotAllowed()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $controller = m::mock(Rest\Controller::class.'[]');
        
        $resp = $controller->_index();
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertEquals(Rest\Response::NOT_ALLOWED, $h[0]);
    }
    
    public function testIndexPostNotAllowed()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $controller = m::mock(Rest\Controller::class.'[]');
        
        $resp = $controller->_index();
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertEquals(Rest\Response::NOT_ALLOWED, $h[0]);
    }
    
    public function testIndexPutNotAllowed()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        
        $controller = m::mock(Rest\Controller::class.'[]');
        
        $resp = $controller->_index();
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertEquals(Rest\Response::NOT_ALLOWED, $h[0]);
    }
    
    public function testIndexDeleteNotAllowed()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        
        $controller = m::mock(Rest\Controller::class.'[]');
        
        $resp = $controller->_index();
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertEquals(Rest\Response::NOT_ALLOWED, $h[0]);
    }
    
    public function testIndexAnyNotAllowed()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'FOO';
        
        $controller = m::mock(Rest\Controller::class.'[]');
        
        $resp = $controller->_index();
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertEquals(Rest\Response::NOT_ALLOWED, $h[0]);
    }
    
    public function testIndexException()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $controller = m::mock(Controllers\Basic::class.'[getOne]')
                ->shouldReceive('getOne')
                ->once()
                ->withArgs(['foo'])
                ->andThrow(new Exception())
                ->getMock()
        ;
        
        $resp = $controller->_index('foo');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertEquals(Rest\Response::INTERNAL_ERROR, $h[0]);
    }
    
    public function testIndexRestException()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $controller = m::mock(Controllers\Basic::class.'[getOne]')
                ->shouldReceive('getOne')
                ->once()
                ->withArgs(['foo'])
                ->andThrow(new Rest\Exception\Conflict('Foo'))
                ->getMock()
        ;
        
        $resp = $controller->_index('foo');
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertEquals(Rest\Response::CONFLICT, $h[0]);        
    }
    
    public function testIndexSeeDetailedErrorIfDevEnv()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $controller = m::mock(Controllers\Basic::class.'[getOne]')
                ->shouldReceive('getOne')
                ->withArgs(['foo'])
                ->andThrow(new RuntimeException('FooBar'))
                ->getMock()
        ;
        
        $controller->devEnv(function() {
            return true;
        });
        
        $resp = $controller->_index('foo', 'bar');
        
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $expected = 'No matching handler found for Mockery_0_Mocks_Controllers_Basic::getOne("bar", "foo"). Either the method was unexpected or its arguments matched no expected argument list for this method';
        $I->assertEquals($expected, $h[1]);
    }
}
