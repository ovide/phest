<?php namespace Ovide\Libs\Mvc\Rest;

use Ovide\Libs\Mvc\FunctionalTester;
use Mocks\Controllers;

class AcceptCest
{
    public function _before(FunctionalTester $I)
    {
        App::instance()->addResources([Controllers\Basic::class, Controllers\Foo::class]);
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function testGet(FunctionalTester $I)
    {
        $I->sendGET('/foo/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(Controllers\Foo::$data));
    }

    public function testGetOne(FunctionalTester $I)
    {
        $I->sendGET('/foo/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson(json_encode(Controllers\Foo::$data[1]));
    }

    public function testNotFound(FunctionalTester $I)
    {
        $I->sendGET('/foo/3');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseEquals(json_encode([
            'message' => '3 not found',
            'code'    => 404,
        ]));
    }

    public function testBadRequest(FunctionalTester $I)
    {
        $I->sendPUT('/foo/2', ['foo' => 'var']);
        $I->seeResponseCodeIs(400);
        $I->seeResponseEquals(json_encode([
            'message' => 'Bad Request',
            'code'    => 400,
        ]));
    }

    public function testForbidden(FunctionalTester $I)
    {
        $I->sendDELETE('/foo/1');
        $I->seeResponseCodeIs(403);
        $I->seeResponseEquals(json_encode([
            'message' => 'I need that resource for testing',
            'code'    => 403,
        ]));
    }

    public function testInternalServerError(FunctionalTester $I)
    {
        $I->sendDELETE('/foo/0');
        $I->seeResponseCodeIs(500);
        $I->seeResponseEquals(json_encode([
            'message' => 'Internal server error',
            'code'    => 500,
        ]));
    }
}
