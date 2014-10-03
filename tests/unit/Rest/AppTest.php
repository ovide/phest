<?php

use Ovide\Libs\Mvc\Rest\App;
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
    
    public function testGetAvailableLanguages()
    {
        $I = $this->tester;
        $rapp = new \ReflectionClass(App::class);
        $ral = $rapp->getProperty('availableLanguages');
        $ral->setAccessible(true);
        $ral->setValue(App::instance(), ['es', 'en']);
        $ral->setAccessible(false);
        
        $langs = App::getAvailableLanguages();
        $I->assertEquals(['es', 'en'], $langs);
    }
    
    /**
     * @depends testGetAvailableLanguages
     */
    public function testAddLanguages()
    {
        $I = $this->tester;
        
        App::addLanguages(['en','es']);
        $I->assertEquals(['en', 'es'], App::getAvailableLanguages());
        App::addLanguages(['en', 'ca']);
        $I->assertEquals(['en', 'es', 'ca'], App::getAvailableLanguages());
    }
    
    public function testAddResourceWithMultipleIds()
    {
        $I = $this->tester;
        
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
}