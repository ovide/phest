<?php

use Ovide\Libs\Mvc\Rest\App;
use Ovide\Libs\Mvc\Rest;
use Mockery as m;

class RestAppTest extends \Codeception\TestCase\Test
{
    /**
    * @var \UnitTester
    */
    protected $tester;

    /**
     * @var App
     */
    protected $app;

    protected function _before()
    {
        $this->app = App::instance();
    }

    protected function _after()
    {
        $rapp = new \ReflectionClass(App::class);
        $ral = $rapp->getProperty('app');
        $ral->setAccessible(true);
        $ral->setValue(null, null);
        $ral->setAccessible(false);
        m::close();
    }

    public function testInstance()
    {
        $I = $this->tester;
        $app = App::instance();
        $I->assertTrue($app === $this->app);
        $I->assertTrue($app instanceof App);
    }

    public function testConstructor()
    {
        $I = $this->tester;
        try {
            $app = new App();
            $I->assertTrue(false);
        } catch (\Exception $ex) {
            $I->assertTrue(true);
        }
    }

    /**
     * @depends testInstance
     */
    public function testAddResource()
    {
        $I = $this->tester;
        $this->app->mountResource(Mocks\Controllers\Basic::class);
        $routes = $this->app->router->getRoutes();
        $route0 = $routes[0];
        $route1 = $routes[1];
        $I->assertEquals('[/]?{id:$}'              , $route0->getPattern());
        $I->assertEquals('/{id:[a-zA-Z0-9_-]+}[/]?', $route1->getPattern());
    }

    /**
     * @depends testAddResource
     */
    public function testAddResourceWithPattern()
    {
        $I = $this->tester;
        $this->app->mountResource(Mocks\Controllers\Foo::class);
        $routes = $this->app->router->getRoutes();
        $route0 = $routes[0];
        $route1 = $routes[1];
        $I->assertEquals('/foo[/]?{id:$}'      , $route0->getPattern());
        $I->assertEquals('/foo/{id:[0-9]+}[/]?', $route1->getPattern());
    }

    /**
     * @depends testAddResource
     */
    public function testAddResources()
    {
        $I = $this->tester;
        $app = App::instance();
        $app->addResources([Mocks\Controllers\Basic::class, Mocks\Controllers\Foo::class]);
        $routes = $this->app->router->getRoutes();
        $route0  = $routes[0];
        $route1  = $routes[1];
        $foo0    = $routes[2];
        $foo1    = $routes[3];
        $I->assertEquals('[/]?{id:$}'              , $route0->getPattern());
        $I->assertEquals('/foo[/]?{id:$}'          , $foo0->getPattern());
        $I->assertEquals('/{id:[a-zA-Z0-9_-]+}[/]?', $route1->getPattern());
        $I->assertEquals('/foo/{id:[0-9]+}[/]?'    , $foo1->getPattern());
    }

    public function testAddResourcesException()
    {
        $I = $this->tester;
        try {
            App::addResources([__CLASS__]);
            $I->assertTrue(false);
        } catch (\Exception $ex) {
            $I->assertTrue(true);
        }
    }

    public function testAddResourceWithMultipleIds()
    {
        $I = $this->tester;

        $app = App::instance();

        $app->addResources([
            Mocks\Controllers\Basic::class,
            Mocks\Controllers\Foo::class,
            Mocks\Controllers\FooVar::class,
        ]);
        $routes = $this->app->router->getRoutes();
        $route0  = $routes[0];
        $route1  = $routes[1];
        $foo0    = $routes[2];
        $foo1    = $routes[3];
        $fooVar0 = $routes[4];
        $fooVar1 = $routes[5];
        $I->assertEquals('[/]?{id:$}'                                     , $route0->getPattern());
        $I->assertEquals('/foo[/]?{id:$}'                                 , $foo0->getPattern());
        $I->assertEquals('/foo/{fooId:[0-9]+}/var[/]?{id:$}'              , $fooVar0->getPattern());
        $I->assertEquals('/{id:[a-zA-Z0-9_-]+}[/]?'                       , $route1->getPattern());
        $I->assertEquals('/foo/{id:[0-9]+}[/]?'                           , $foo1->getPattern());
        $I->assertEquals('/foo/{fooId:[0-9]+}/var/{id:[a-zA-Z0-9_-]+}[/]?', $fooVar1->getPattern());
    }

    public function testNotFound()
    {
        $I = $this->tester;

        //We need at least a resource to init the routes handler
        $app = App::instance();
        $app->mountResource(Mocks\Controllers\Foo::class);
        /* @var $res \Igm\Rest\Response */
        $res = $app->handle('/bar');

        $this->assertInstanceOf(\Ovide\Libs\Mvc\Rest\Response::class, $res);
        $I->assertEquals('404 Not Found', $res->getHeaders()->get('Status'));
    }

    public function testAddRequestHeaderBadClassName()
    {
        $I = $this->tester;
        $app = App::instance();
        try {
            $app->addHeaderHandler(Phalcon\Mvc\Controller::class);
            $I->assertTrue(false);
        } catch (LogicException $ex) {
            $I->assertTrue(true);
        }
    }

    public function testAddRequestHeader()
    {
        $I = $this->tester;

        $app = App::instance();
        $app->mountResource(Mocks\Controllers\Basic::class);
        $app->addHeaderHandler(Mocks\Headers\Basic::class);
        $app->handle('/foo');

        $I->assertEquals(1, Mocks\Headers\Basic::$_initCalled);
        $I->assertEquals(0, Mocks\Headers\Basic::$_handleCalled);

        Mocks\Headers\Basic::$_initCalled = 0;
    }

    /**
     * @depends testAddRequestHeader
     */
    public function testHandleRequestHeader()
    {
        $I = $this->tester;

        $_SERVER['FOO'] = 'bar';
        $app = App::instance();
        $app->mountResource(Mocks\Controllers\Basic::class);
        $app->addHeaderHandler(Mocks\Headers\Basic::class);
        $app->handle('/foo');

        $I->assertEquals(1, Mocks\Headers\Basic::$_initCalled);
        $I->assertEquals(1, Mocks\Headers\Basic::$_handleCalled);

        Mocks\Headers\Basic::$_initCalled   = 0;
        Mocks\Headers\Basic::$_handleCalled = 0;
    }

    public function testSetConfig()
    {
        $I = $this->tester;

        $app = App::instance();
        $app->setConfig('foo', 'bar', 'var');

        $this->assertEquals(['foo' => ['bar' => 'var']],
                PHPUnit_Framework_Assert::readAttribute($app, '_config'));
    }

    /**
     * @depends testSetConfig
     */
    public function testGetConfig()
    {
        $I = $this->tester;

        $app = App::instance();
        $app->setConfig('foo', 'bar', 'var');

        $actual   = $app->getConfig('foo', 'bar');
        $expected = 'var';

        $I->assertEquals($expected, $actual);
    }

    public function testMountRoutesNotCached()
    {
        $rc = new ReflectionClass(App::class);
        $prop = $rc->getProperty('app');
        $prop->setAccessible(true);
        $prop->setValue(null, null);

        $mock = $this->getMockBuilder(App::class)->setMethods(['mountResource'])->getMock();
        $mock
            ->expects($this->exactly(2))
            ->method('mountResource')
            ->withConsecutive([$this->equalTo(\Mocks\Controllers\FooVar::class)],[Mocks\Controllers\Basic::class])
        ;

        $mock->addResources([\Mocks\Controllers\FooVar::class, Mocks\Controllers\Basic::class]);
    }

    public function testMountRoutesCached()
    {
        $rc = new ReflectionClass(App::class);
        $prop = $rc->getProperty('app');
        $prop->setAccessible(true);
        $prop->setValue(null, null);

        $cached = [
            'handlers' => 'handlers',
            'router'   => new stdClass()
        ];
        $stub = $this->getMockBuilder(stdClass::class)->setMethods(['get'])->getMock();
        $stub->expects($this->once())->method('get')->with($this->anything())->willReturn($cached);

        $app = App::instance();
        $di = $app->getDI();
        $di->setShared('cache', $stub);

        $app->addResources([\Mocks\Controllers\FooVar::class, Mocks\Controllers\Basic::class]);
        $this->assertEquals('handlers', $app->getHandlers());
        $this->assertInstanceOf(stdClass::class, $app->getRouter());
        $this->assertSame($app->getRouter(), $app->getDI()->getShared('router'));
    }

    public function testMountRoutesSaveCache()
    {
        $rc = new ReflectionClass(App::class);
        $prop = $rc->getProperty('app');
        $prop->setAccessible(true);
        $prop->setValue(null, null);
        $app = App::instance();

        $stub = $this->getMockBuilder(stdClass::class)->setMethods(['get', 'save'])->getMock();
        $stub->expects($this->once())->method('get')->with($this->anything())->willReturn(false);
        $stub->expects($this->once())->method('save')->with($this->anything(), $this->callback(
            function(array $param) use ($app){
                return ($param['router'] === $app->getRouter() && $param['handlers'] === $app->getHandlers());
            }
        ));

        $di = $app->getDI();
        $di->setShared('cache', $stub);

        $app->addResources([\Mocks\Controllers\FooVar::class, Mocks\Controllers\Basic::class]);
    }
}
