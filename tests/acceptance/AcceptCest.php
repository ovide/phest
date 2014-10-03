<?php
use \AcceptanceTester;
use Ovide\Libs\Mvc\Rest;

require_once __DIR__.'/../mocks/BasicMock.php';
require_once __DIR__.'/../mocks/FooMock.php';

class AcceptCest
{
    public function _before(AcceptanceTester $I)
    {
        Rest\App::addResources([
            'basic'     => BasicMock::class,
            'basic/foo' => FooMock::class
        ]);
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
    
    public function testNotFound(AcceptanceTester $I)
    {
        $I->sendGET('/basic/foo/3');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseEquals(json_encode([
            'message' => '3 not found',
            'code'    => 404
        ]));
    }
    
    public function testBadRequest(AcceptanceTester $I)
    {
        $I->sendPUT('/basic/foo/2', ['foo' => 'var']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseEquals(json_encode([
            'message' => 'Bad Request',
            'code'    => 400
        ]));
    }
    
    public function testForbidden(AcceptanceTester $I)
    {
        $I->sendDELETE('/basic/foo/1');
        $I->seeResponseCodeIs(403);
        $I->seeResponseEquals(json_encode([
            'message' => 'I need that resource for testing',
            'code'    => 403
        ]));
    }
    
    public function testInternalServerError(AcceptanceTester $I)
    {
        $I->sendDELETE('/basic/foo/0');
        $I->seeResponseCodeIs(500);
        $I->seeResponseEquals(json_encode([
            'message' => 'Internal server error',
            'code'    => 500
        ]));
    }
}