<?php
use \AcceptanceTester;
use Ovide\Libs\Mvc\Rest;
use Mocks\Controllers;

class BasicCest
{
    public function _before(AcceptanceTester $I)
    {
        Rest\App::addResources([
            'basic' => Controllers\Foo::class
        ]);
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests

    public function testGet(AcceptanceTester $I)
    {
        $I->sendGET('/basic');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(Controllers\Foo::$data));
    }

    /**
     * @depends testGet
     * @param AcceptanceTester $I
     */
    public function testGetOne(AcceptanceTester $I)
    {
        $I->sendGET('/basic/1');
        $I->seeResponseCodeIs(200);
        $expected = Controllers\Foo::$data[0];
        $I->seeResponseEquals(json_encode($expected));
    }

    public function testPost(AcceptanceTester $I)
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

    public function testPut(AcceptanceTester $I)
    {
        $put = ['id' => 3, 'name' => 'Put', 'description' => 'PUT'];
        $I->sendPUT('/basic/3', $put);
        $I->seeResponseCodeIs(204);
    }

    public function testDelete(AcceptanceTester $I)
    {
        $I->sendDELETE('/basic/3');
        $I->seeResponseCodeIs(204);
    }
    
    /**
     * @depends testGet
     * @param AcceptanceTester $I
     */
    public function testNotFound(AcceptanceTester $I)
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
    public function testError500(AcceptanceTester $I)
    {
        $I->sendGET('/basic/dsadf');
        $expected = [
            'message' => Rest\Response::$status[Rest\Response::INTERNAL_ERROR],
            'code'    => Rest\Response::INTERNAL_ERROR,
        ];
        $I->seeResponseCodeIs(Rest\Response::INTERNAL_ERROR);
        $I->seeResponseContainsJson($expected);
    }
    
    public function testNotAllowed(AcceptanceTester $I)
    {
        $I->sendAjaxRequest('FOO', '/basic');
        $I->seeResponseCodeIs(405);
    }
}