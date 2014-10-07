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
    
    protected function _before()
    {
        Rest\App::instance();
        //$_SERVER['REQUEST_URI'] = 'logs';
        //$_GET["_url"] = '/logs';
    }

    protected function _after()
    {
        m::close();
        $_SERVER['REQUEST_METHOD'] = null;
    }

    
    public function testGetOneNoArgs()
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
    
    public function testGetOneWithArgs()
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
    
    public function testGetNoArgs()
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
    
    public function testGetWithArgs()
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
    
    public function testPostNoArgs()
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
    
    public function testPostWithArgs()
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
    
    public function testPutNoArgs()
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
    
    public function testPutWithArgs()
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
    
    public function testDeleteNoArgs()
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
    
    public function testDeleteWithArgs()
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
    
    public function testOptions()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        
        $controller = m::mock(Controllers\Basic::class.'[options]')
                ->shouldReceive('options')
                ->once()
                ->withNoArgs()
                ->getMock()
        ;
        
        $resp = $controller->_index('');
    }
    
    public function testOptionsAllAllowed()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        
        $controller = new Controllers\Basic();
        
        $resp = $controller->_index();
        
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $I->assertEquals(200, $h[0],
            "$status: May didn't call the mock method"
        );
        $actual = $resp->getHeaders()->get('Allow');
        $I->assertEquals('DELETE, GET, POST, PUT', $actual);
    }
    
    public function testOptionsSomeAllowed()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        
        $controller = $this->getMockForAbstractClass(
            Rest\Controller::class,
            [], '', true, true, true, ['get', 'put']
        );
        
        $resp = $controller->_index();

        $actual = $resp->getHeaders()->get('Allow');
        $I->assertEquals('GET, PUT', $actual);
    }
    
    public function testOptionsWithAcl()
    {
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';

        $resource = new \Phalcon\Acl\Resource('/foo');
        $role     = new \Phalcon\Acl\Role('foo');
        $acl      = new Phalcon\Acl\Adapter\Memory();
        $acl->setDefaultAction(Phalcon\Acl::DENY);
        $acl->addResource($resource);
        $acl->addRole($role);
        $acl->addResourceAccess($resource->getName(), 
                ['GET', 'POST', 'PUT', 'DELETE']);
        $acl->allow($role->getName(), $resource->getName(), 'GET');
        $acl->allow($role->getName(), $resource->getName(), 'POST');
        $acl->isAllowed($role->getName(), $resource->getName(), 'GET');
        $app = Rest\App::instance();
        //$app->di->set('acl', $acl);
        $app->setService('acl', $acl);
        
        $controller = $this->getMockForAbstractClass(
            Rest\Controller::class,
            [], '', true, true, true, ['get', 'put']
        );
        $controller->setDI($app->di);

        $resp = $controller->_index();

        $actual = $resp->getHeaders()->get('Allow');
        $I->assertEquals('GET', $actual);        
    }
    
    public function testOptionsNothingAllowed()
    {
        
    }
    
    public function testGetNotAllowed()
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
    
    public function testPostNotAllowed()
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
    
    public function testPutNotAllowed()
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
    
    public function testDeleteNotAllowed()
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
    
    public function testAnyNotAllowed()
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
    
    public function testException()
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
    
    public function testRestException()
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
    
    public function testSeeDetailedErrorIfDevEnv()
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
        
        Rest\Controller::devEnv(function() { return true; });
        
        $resp = $controller->_index('foo', 'bar');
        
        Rest\Controller::devEnv(function(){ return false; });
        
        $I->assertTrue($resp instanceof \Phalcon\Http\Response);
        $status = $resp->getHeaders()->get('Status');
        $h = explode(' ', $status, 2);
        $expected = 'No matching handler found for Mockery_0_Mocks_Controllers_Basic::getOne("bar", "foo"). Either the method was unexpected or its arguments matched no expected argument list for this method';
        $I->assertEquals($expected, $h[1]);
    }


    public function testRegisterHeaders()
    {
        //TODO
        /* @var $controller Rest\Controller */
        $I = $this->tester;
        
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        //$headerClass = m::mock(Mocks\Headers\Basic::class)
        //        ->shouldReceive('__construct')
        //        ->once()
        //;
        //$c = new $headerClass();
        
        //Rest\Controller::registerHeaders([$headerClass]);
    }
}
