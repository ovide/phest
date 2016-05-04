<?php

use Ovide\Libs\Mvc\Rest\App;

class RestAppTest extends \Codeception\TestCase\Test
{
    /**
    * @var Ovide\Libs\Mvc\UnitTester
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
            $this->app->addResources([__CLASS__]);
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
        /* @var $res \Ovide\Libs\Mvc\Rest\Response */
        $res = $app->handle('/bar');

        $this->assertInstanceOf(\Ovide\Libs\Mvc\Rest\Response::class, $res);
        $I->assertEquals('404 Not Found', $res->getHeaders()->get('Status'));
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

    public function testAddHeaderHandler()
    {
        $hh = $this->getMockForAbstractClass(\Ovide\Libs\Mvc\Rest\Middleware::class);
        $this->app->addHeaderHandler($hh);

        $em = $this->app->getEventsManager()->getListeners('micro');

        $this->assertContains($hh, $em);
    }

    public function testErrorHandler()
    {
        $this->app->getEventsManager()->attach('micro', function(\Phalcon\Events\Event $evt, App $app) {
            if ($evt->getType() === 'beforeHandleRoute') {
                throw new \Exception('foo', 555, new \Exception('bar', 444));
            }
        });

        $response = $this->app->handle('/');
        $expected = ['message' => 'Internal server error', 'code' => 500];
        $this->assertEquals($expected, json_decode($response->getContent(), true));
        $this->assertEquals('500 Internal server error', $response->getHeaders()->get('Status'));
    }

    public function testErrorHandlerInDevMode()
    {
        $this->app->getEventsManager()->attach('micro', function(\Phalcon\Events\Event $evt, App $app) {
            if ($evt->getType() === 'beforeHandleRoute') {
                throw new \Exception('foo', 555, new \Exception('bar', 444));
            }
        });

        $this->app->devEnv = true;

        $response = $this->app->handle('/');
        $actualContent   = json_decode($response->getContent());
        $this->assertEquals(555, $actualContent->code);
        $this->assertEquals('foo', $actualContent->message);
        $this->assertEquals('Exception', $actualContent->type);
        $this->assertInternalType('array', $actualContent->trace);
        $this->assertEquals('500 foo', $response->getHeaders()->get('Status'));
    }
}
