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
        $this->app->addResource('route', Mocks\Controllers\Basic::class);
        $routes = $this->app->router->getRoutes();
        $route = $routes[0];
        /* @var $route \Phalcon\Mvc\Router\Route */
        $I->assertEquals('/route[/]?{id:[a-zA-Z0-9_-]*}[/]?', $route->getPattern());
    }
    
    /**
     * @depends testAddResource
     */
    public function testAddResourceWithPattern()
    {
        $I = $this->tester;
        $this->app->addResource('route', Mocks\Controllers\Basic::class, '[0-9]*');
        $routes = $this->app->router->getRoutes();
        $route = $routes[0];
        /* @var $route \Phalcon\Mvc\Router\Route */
        $I->assertEquals('/route[/]?{id:[0-9]*}[/]?', $route->getPattern());
    }
    
    /**
     * @depends testAddResource
     */
    public function testAddResources()
    {
        $I = $this->tester;
        App::addResources([
            'route' => Mocks\Controllers\Basic::class,
            'foo'   => Mocks\Controllers\Basic::class
        ]);
        $routes = $this->app->router->getRoutes();
        $route  = $routes[0];
        $foo    = $routes[1];
        $I->assertEquals('/route[/]?{id:[a-zA-Z0-9_-]*}[/]?', $route->getPattern());
        $I->assertEquals('/foo[/]?{id:[a-zA-Z0-9_-]*}[/]?', $foo->getPattern());
    }
    
    public function testAddResourcesException()
    {
        $I = $this->tester;
        try {
            App::addResources([
                'route' => __CLASS__,
            ]);
            $I->assertTrue(false);
        } catch (\Exception $ex) {
            $I->assertTrue(true);
        }
    }
    
    public function testAddResourceWithMultipleIds()
    {
        $I = $this->tester;
        
        App::addResources([
            'route'              => Mocks\Controllers\Basic::class,
            'foo'                => Mocks\Controllers\Foo::class,
            'foo/{fooId:[0-9]*}' => Mocks\Controllers\FooVar::class,
        ]);
        $routes = $this->app->router->getRoutes();
        $route  = $routes[0];
        $foo    = $routes[1];
        $fooVar = $routes[2];
        $I->assertEquals('/route[/]?{id:[a-zA-Z0-9_-]*}[/]?', $route->getPattern());
        $I->assertEquals('/foo[/]?{id:[a-zA-Z0-9_-]*}[/]?', $foo->getPattern());
        $I->assertEquals('/foo/{fooId:[0-9]*}[/]?{id:[a-zA-Z0-9_-]*}[/]?', $fooVar->getPattern());
    }
    
    public function testNotFound()
    {
        $I = $this->tester;

        //We need at least a resource to init the routes handler
        $app = App::instance();
        $app->addResource('var', Mocks\Controllers\Basic::class);
        /* @var $res \Ovide\Libs\Mvc\Rest\Response */
        $res = $app->handle('/foo');
        
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
        $app->addResource('foo', Mocks\Controllers\Basic::class);
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
        $app->addResource('foo', Mocks\Controllers\Basic::class);
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
}