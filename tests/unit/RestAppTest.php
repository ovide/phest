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
}