<?php namespace Ovide\Libs\Mvc\Rest;

use Ovide\Libs\Mvc\FunctionalTester;
use Mocks\Controllers;


class BasicCest
{
    public function _before(FunctionalTester $I)
    {
        App::addResources([
            'basic' => Controllers\Foo::class
        ]);
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests

    public function testGet(FunctionalTester $I)
    {
        $I->sendGET('/basic');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(Controllers\Foo::$data));
    }

    /**
     * @depends testGet
     * @param AcceptanceTester $I
     */
    public function testGetOne(FunctionalTester $I)
    {
        $I->sendGET('/basic/1');
        $I->seeResponseCodeIs(200);
        $expected = Controllers\Foo::$data[0];
        $I->seeResponseEquals(json_encode($expected));
    }

    public function testPost(FunctionalTester $I)
    {
        $post    = ['name' => 'Post', 'description' => 'PostDesc'];
        $prepend = ['id' => 3];
        $I->sendPOST('/basic/', $post);
        $prepend += $post;
        $I->seeResponseCodeIs(201);
        //TODO
        //$I->seeHttpHeader('Location', '/basic/3');
        $I->seeResponseEquals(json_encode($prepend));
    }

    public function testPut(FunctionalTester $I)
    {
        $put = ['id' => 3, 'name' => 'Put', 'description' => 'PUT'];
        $I->sendPUT('/basic/3', $put);
        $I->seeResponseCodeIs(204);
    }

    public function testDelete(FunctionalTester $I)
    {
        $I->sendDELETE('/basic/3');
        $I->seeResponseCodeIs(204);
    }

    /**
     * @depends testGet
     * @param AcceptanceTester $I
     */
    public function testNotFound(FunctionalTester $I)
    {
        $I->sendGET('/');
        $I->seeResponseCodeIs(404);
        $I->seeResponseEquals(json_encode([
            'message' => 'Not Found',
            'code'    => 404
        ]));
    }

    /**
     * @depends testGetOne
     * @param AcceptanceTester $I
     */
    public function testError500(FunctionalTester $I)
    {
        $I->sendGET('/basic/dsadf');
        $expected = [
            'message' => Response::$status[Response::INTERNAL_ERROR],
            'code'    => Response::INTERNAL_ERROR,
        ];
        $I->seeResponseCodeIs(Response::INTERNAL_ERROR);
        $I->seeResponseContainsJson($expected);
    }

    public function testNotAllowed(FunctionalTester $I)
    {
        $I->sendAjaxRequest('FOO', '/basic');
        $I->seeResponseCodeIs(405);
    }
}