<?php
use \AcceptanceTester;
use Ovide\Libs\Mvc\Rest;

class BasicCest
{
    public function _before(AcceptanceTester $I)
    {
        $app = Rest\App::instance();
        $app->addResource('basic', BasicMock::class);
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests

    public function testGet(AcceptanceTester $I)
    {
        $I->sendGET('/basic');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(BasicMock::$data));
    }

    /**
     * @depends testGet
     * @param AcceptanceTester $I
     */
    public function testGetOne(AcceptanceTester $I)
    {
        $I->sendGET('/basic/1');
        $I->seeResponseCodeIs(200);
        $expected = BasicMock::$data[0];
        $I->seeResponseEquals(json_encode($expected));
    }

    public function testPost(AcceptanceTester $I)
    {
        $post = ['id' => 3, 'name' => 'Post'];
        $I->sendPOST('/basic/', $post);
        $I->seeResponseCodeIs(201);
        $I->seeResponseEquals(json_encode($post));
    }

    public function testPut(AcceptanceTester $I)
    {
        $put = ['id' => 3, 'name' => 'Put'];
        $I->sendPUT('/basic/3', $put);
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode($put));
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
        $I->sendGET('/basic/4');
        $expected = [
            'message' => 'Undefined offset: 3',
            'code'    => 0,
            //'type'    => 'ErrorException',
            //'line'    => 28
        ];
        $I->seeResponseCodeIs(500);
        $I->seeResponseContainsJson($expected);
    }
    
    public function testNotAllowed(AcceptanceTester $I)
    {
        $I->sendAjaxRequest('FOO', '/basic');
        $I->seeResponseCodeIs(405);
    }
}