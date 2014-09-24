<?php namespace Ovide\Libs\Mvc\Rest;

require_once __DIR__.'/../mocks/BasicMock.php';
require_once __DIR__.'/../mocks/FooMock.php';

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
        $this->app->router->clear();
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
        $this->app->addResource('route', \BasicMock::class);
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
        $this->app->addResource('route', \BasicMock::class, '[0-9]*');
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
            'route' => \BasicMock::class,
            'foo'   => \FooMock::class
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
}