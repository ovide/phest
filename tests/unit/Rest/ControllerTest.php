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
        Rest\App::instance()->reset();
        $di = Rest\App::instance()->di;
        $di->setShared('requestReader' , Rest\ContentType\Json::class);
        $di->setShared('responseWriter', Rest\ContentType\Json::class);
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

        $resp = $controller->handle('foo');
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
                ->withArgs(['foo', 'var', 'foovar'])
                ->getMock()
        ;

        $resp = $controller->handle('foo', 'var', 'foovar');
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

        $resp = $controller->handle('');
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

        $resp = $controller->handle('foo', 'var', '');
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

        $resp = $controller->handle('');
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

        $resp = $controller->handle('foo', 'var', '');
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

        $resp = $controller->handle('foo');
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

        $resp = $controller->handle('foo', 'var', 'foovar');
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

        $resp = $controller->handle('foo');
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
                ->withArgs(['foo', 'var', 'foovar'])
                ->getMock()
        ;

        $resp = $controller->handle('foo','var','foovar');
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

        $resp = $controller->handle('');
    }

    public function testOptionsAllAllowed()
    {
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';

        $controller = new Controllers\Basic();
        $controller->setDI(Rest\App::instance()->getDI());

        $resp = $controller->handle();

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

        $controller->setDI(Rest\App::instance()->getDI());

        $resp = $controller->handle();

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
        $acl->addResource($resource, []);
        $acl->addRole($role);
        $acl->addResourceAccess($resource->getName(),
                ['GET', 'POST', 'PUT', 'DELETE']);
        $acl->allow($role->getName(), $resource->getName(), 'GET');
        $acl->allow($role->getName(), $resource->getName(), 'POST');
        $acl->isAllowed($role->getName(), $resource->getName(), 'GET');
        $app = Rest\App::instance();

        $app->setService('acl', $acl, true);

        $controller = $this->getMockForAbstractClass(
            Rest\Controller::class,
            [], '', true, true, true, ['get', 'put']
        );
        $controller->setDI($app->getDI());

        $resp = $controller->handle();

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

        try {
            $controller->handle();
            $this->assertTrue(false, "Should throw a exception");
        } catch (Rest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Rest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Rest\Response::$status[Rest\Response::NOT_ALLOWED], $ex->getMessage());
        }
    }

    public function testPostNotAllowed()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'POST';

        $controller = m::mock(Rest\Controller::class.'[]');

        try {
            $controller->handle();
            $this->assertTrue(false, "Should throw a exception");
        } catch (Rest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Rest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Rest\Response::$status[Rest\Response::NOT_ALLOWED], $ex->getMessage());
        }
    }

    public function testPutNotAllowed()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $controller = m::mock(Rest\Controller::class.'[]');

        try {
            $controller->handle();
            $this->assertTrue(false, "Should throw a exception");
        } catch (Rest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Rest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Rest\Response::$status[Rest\Response::NOT_ALLOWED], $ex->getMessage());
        }
    }

    public function testDeleteNotAllowed()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $controller = m::mock(Rest\Controller::class.'[]');

        try {
            $controller->handle();
            $this->assertTrue(false, "Should throw a exception");
        } catch (Rest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Rest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Rest\Response::$status[Rest\Response::NOT_ALLOWED], $ex->getMessage());
        }
    }

    public function testAnyNotAllowed()
    {
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'FOO';

        $controller = m::mock(Rest\Controller::class.'[]');

        try {
            $controller->handle('foo');
            $this->assertTrue(false, "Should throw a exception");
        } catch (Rest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Rest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Rest\Response::$status[Rest\Response::NOT_ALLOWED], $ex->getMessage());
        }
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
                ->andThrow(new Exception('foo'))
                ->getMock()
        ;

        try {
            $controller->handle('foo');
            $this->assertTrue(false, "Should throw a exception");
        } catch (\Exception $ex) {
            $this->assertEquals('foo', $ex->getMessage());
        }
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

        try {
            $controller->handle('foo');
        } catch (\Ovide\Libs\Mvc\Rest\Exception\Conflict $ex) {
            $this->assertEquals(Rest\Response::CONFLICT, $ex->getCode());
            $this->assertEquals('Foo', $ex->getMessage());
        }
    }



    public function testSetLocationAfterPost()
    {
        /* @var $controller Rest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/foo';

        $controller = m::mock(Controllers\Basic::class.'[post]')
                ->shouldReceive('post')
                ->andReturn(['id' => 'bar', 'content' => 'foo'])
                ->getMock()
        ;
        $resp = $controller->handle();
        $location = $resp->getHeaders()->get('Location');
        $I->assertEquals('/foo/bar', $location);
    }
}
