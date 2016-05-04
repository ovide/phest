<?php namespace Ovide\Libs\Mvc\Rest;

use Ovide\Libs\Mvc\FunctionalTester;
use Mocks\Controllers;

class BasicCest
{
    public function _before(FunctionalTester $I)
    {
        App::instance()->addResources([Controllers\Foo::class]);
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests

    public function testGet(FunctionalTester $I)
    {
        $I->sendGET('/foo');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(Controllers\Foo::$data));
    }

    /**
     * @depends testGet
     * @param AcceptanceTester $I
     */
    public function testGetOne(FunctionalTester $I)
    {
        $I->sendGET('/foo/1');
        $I->seeResponseCodeIs(200);
        $expected = Controllers\Foo::$data[0];
        $I->seeResponseEquals(json_encode($expected));
    }

    public function testPost(FunctionalTester $I)
    {
        $post    = ['name' => 'Post', 'description' => 'PostDesc'];
        $prepend = ['id' => 3];
        $I->sendPOST('/foo/', $post);
        $prepend += $post;
        $I->seeResponseCodeIs(201);
        //TODO
        //$I->seeHttpHeader('Location', '/basic/3');
        $I->seeResponseEquals(json_encode($prepend));
    }

    public function testPut(FunctionalTester $I)
    {
        $put = ['id' => 3, 'name' => 'Put', 'description' => 'PUT'];
        $I->sendPUT('/foo/3', $put);
        $I->seeResponseCodeIs(204);
    }

    public function testDelete(FunctionalTester $I)
    {
        $I->sendDELETE('/foo/3');
        $I->seeResponseCodeIs(204);
    }

    /**
     * @depends testGet
     * @param AcceptanceTester $I
     */
    public function testNotFound(FunctionalTester $I)
    {
        $I->sendGET('/foobar/foo/bar');
        $I->seeResponseCodeIs(404);
        $I->seeResponseEquals(json_encode([
            'message' => 'Not Found',
            'code'    => 404,
        ]));
    }

    public function testNotAllowed(FunctionalTester $I)
    {
        $I->sendAjaxRequest('FOO', '/basic');
        $I->seeResponseCodeIs(405);
    }
}
