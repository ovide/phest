<?php

use Mockery as m;
use Ovide\Phest;
use Mocks\Controllers;

class ControllerTest extends \Codeception\TestCase\Test
{
    /**
    * @var UnitTester
    */
    protected $tester;

    protected function _before()
    {
        Phest\App::instance()->reset();
        $di = Phest\App::instance()->di;
        $di->setShared('requestReader' , Phest\ContentType\Json::class);
        $di->setShared('responseWriter', Phest\ContentType\Json::class);
    }

    protected function _after()
    {
        m::close();
        $_SERVER['REQUEST_METHOD'] = null;
    }

    public function testGetOneNoArgs()
    {
        /* @var $controller Phest\Controller */
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
        Phest\App::reset();
        $di = \Phalcon\Di\FactoryDefault::getDefault();
        $di->remove('acl');
        $controller->setDI($di);

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
            Phest\Controller::class,
            [], '', true, true, true, ['get', 'put']
        );

        $controller->setDI(Phest\App::instance()->getDI());

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
        $app = Phest\App::instance();

        $app->getDI()->setShared('acl', $acl);
        $app->setService('acl', $acl, true);

        $controller = $this->getMockForAbstractClass(
            Phest\Controller::class,
            [], '', true, true, true, ['get', 'put']
        );
        $controller->di = $app->getDI();
        //$controller->setDI($app->getDI());

        $resp = $controller->handle();

        $actual = $resp->getHeaders()->get('Allow');
        $I->assertEquals('GET', $actual);
    }

    public function testOptionsNothingAllowed()
    {
    }

    public function testGetNotAllowed()
    {
        /* @var $controller Phest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $controller = m::mock(Phest\Controller::class.'[]');

        try {
            $controller->handle();
            $this->assertTrue(false, "Should throw a exception");
        } catch (Phest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Phest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Phest\Response::$status[Phest\Response::NOT_ALLOWED], $ex->getMessage());
        }
    }

    public function testPostNotAllowed()
    {
        /* @var $controller Phest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'POST';

        $controller = m::mock(Phest\Controller::class.'[]');

        try {
            $controller->handle();
            $this->assertTrue(false, "Should throw a exception");
        } catch (Phest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Phest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Phest\Response::$status[Phest\Response::NOT_ALLOWED], $ex->getMessage());
        }
    }

    public function testPutNotAllowed()
    {
        /* @var $controller Phest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $controller = m::mock(Phest\Controller::class.'[]');

        try {
            $controller->handle();
            $this->assertTrue(false, "Should throw a exception");
        } catch (Phest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Phest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Phest\Response::$status[Phest\Response::NOT_ALLOWED], $ex->getMessage());
        }
    }

    public function testDeleteNotAllowed()
    {
        /* @var $controller Phest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $controller = m::mock(Phest\Controller::class.'[]');

        try {
            $controller->handle();
            $this->assertTrue(false, "Should throw a exception");
        } catch (Phest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Phest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Phest\Response::$status[Phest\Response::NOT_ALLOWED], $ex->getMessage());
        }
    }

    public function testAnyNotAllowed()
    {
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'FOO';

        $controller = m::mock(Phest\Controller::class.'[]');

        try {
            $controller->handle('foo');
            $this->assertTrue(false, "Should throw a exception");
        } catch (Phest\Exception\MethodNotAllowed $ex) {
            $this->assertEquals(Phest\Response::NOT_ALLOWED, $ex->getCode());
            $this->assertEquals(Phest\Response::$status[Phest\Response::NOT_ALLOWED], $ex->getMessage());
        }
    }

    public function testException()
    {
        /* @var $controller Phest\Controller */
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
        /* @var $controller Phest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $controller = m::mock(Controllers\Basic::class.'[getOne]')
                ->shouldReceive('getOne')
                ->once()
                ->withArgs(['foo'])
                ->andThrow(new Phest\Exception\Conflict('Foo'))
                ->getMock()
        ;

        try {
            $controller->handle('foo');
        } catch (\Ovide\Phest\Exception\Conflict $ex) {
            $this->assertEquals(Phest\Response::CONFLICT, $ex->getCode());
            $this->assertEquals('Foo', $ex->getMessage());
        }
    }



    public function testSetLocationAfterPost()
    {
        /* @var $controller Phest\Controller */
        $I = $this->tester;

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/foo';

        $controller = m::mock(Controllers\Basic::class.'[post]')
                ->shouldReceive('post')
                ->andReturn(['id' => 'bar', 'content' => 'foo'])
                ->getMock();
        
        $controller->di->set('responseWriter', Phest\ContentType\Json::class, true);
        $resp = $controller->handle();
        $location = $resp->getHeaders()->get('Location');
        $I->assertEquals('/foo/bar', $location);
    }
}
