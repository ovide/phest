<?php
use \AcceptanceTester;
use Ovide\Libs\Mvc\Rest;

class AcceptCest
{
    public function _before(AcceptanceTester $I)
    {
        require_once __DIR__.'/../mocks/BasicMock.php';
        require_once __DIR__.'/../mocks/FooMock.php';
        Rest\App::addResourceArray(array(
            'basic'     => BasicMock::class,
            'basic/foo' => FooMock::class
        ));
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testGet(AcceptanceTester $I)
    {
        $I->sendGET('/basic/foo/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(FooMock::$data));
    }
    
    public function testGetOne(AcceptanceTester $I)
    {
        $I->sendGET('/basic/foo/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson(json_encode(FooMock::$data[1]));
    }
    
    public function getOneNotFound(AcceptanceTester $I)
    {
        $I->sendGET('/basic/foo/3');
        $resp = $I->grabResponse();
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $resp = $I->grabResponse();
    }
}