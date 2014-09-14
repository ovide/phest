<?php

use ovide\libs\mvc\RestApp;

class RestAppTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /**
     * @var RestApp
     */
    protected $app;

    protected function _before()
    {
        $this->app = RestApp::instance();
    }

    protected function _after()
    {
    }

    // tests
    public function testGetOne()
    {
        require_once __DIR__.'/../mocks/BasicMock.php';
        RestApp::addResource('/', BasicMock::class);
        $res = $this->app->handle('/');
    }

}