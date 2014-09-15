<?php
use \AcceptanceTester;

class AcceptCest
{
    public function _before(AcceptanceTester $I)
    {
        require_once __DIR__.'/../mocks/BasicMock.php';
        Ovide\Libs\Mvc\RestApp::addResource('basic', BasicMock::class);
    }

    public function _after(AcceptanceTester $I)
    {
    }

    //tests
    public function testAll(AcceptanceTester $I)
    {
               
    }
}