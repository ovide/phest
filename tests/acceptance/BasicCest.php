<?php
use \AcceptanceTester;

class BasicCest
{
    public function _before(AcceptanceTester $I)
    {
        require_once __DIR__.'/../mocks/BasicMock.php';
        Ovide\Libs\Mvc\RestApp::addResource('basic', BasicMock::class);
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests

    public function testGet(AcceptanceTester $I)
    {
        $I->sendGET('/basic');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(BasicMock::$data);
    }

    /**
     * @depends testGet
     * @param AcceptanceTester $I
     */
    public function testGetOne(AcceptanceTester $I)
    {
        $I->sendGET('/basic/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(BasicMock::$data[0]);

    }

    public function testPost(AcceptanceTester $I)
    {
        $post = ['id' => 3, 'name' => 'Post'];
        $I->sendPOST('/basic/', $post);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson(['id' => 3, 'name' => 'Post']);
    }

    public function testPut(AcceptanceTester $I)
    {
        $put = ['id' => 3, 'name' => 'Put'];
        $I->sendPUT('/basic/3', $put);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson($put);
    }

    public function testDelete(AcceptanceTester $I)
    {
        $I->sendDELETE('/basic/3');
        $I->seeResponseCodeIs(204);
    }
    
    public function testNotFound(AcceptanceTester $I)
    {
        $I->sendGET('/');
        $I->seeResponseCodeIs(404);
        $I->seeResponseContainsJson(['message' => 'Not Found']);
    }
}