<?php
use \AcceptanceTester;

class BasicCest
{
    public function _before(AcceptanceTester $I)
    {
        require_once __DIR__.'/../mocks/BasicMock.php';
        ovide\libs\mvc\RestApp::addResource('basic', BasicMock::class);
    }

    public function _after(AcceptanceTester $I)
    {
        $app = ovide\libs\mvc\RestApp::instance();
        unset($app);
        //$app->response = null;
        //$app->request  = null;
    }

    // tests

    public function testGet(AcceptanceTester $I)
    {
        $I->amOnPage('/basic');
        $I->seeResponseCodeIs(200);
        $I->see('[{"id":1,"name":"Foo"},{"id":2,"name":"Var"}]');
    }

    /**
     * @depends testGet
     * @param AcceptanceTester $I
     */
    public function testGetOne(AcceptanceTester $I)
    {
        $I->amOnPage('/basic/1');
        $I->seeResponseCodeIs(200);
        $I->see('{"id":1,"name":"Foo"}');

    }

    public function post(AcceptanceTester $I)
    {
        $I->sendAjaxRequest('POST', '/basic/', ['id' => 3, 'name' => 'Post']);
        $I->seeResponseCodeIs(201);
        $I->see('{"id":3,"name":"Post"}');
    }

    public function put(AcceptanceTester $I)
    {
        $I->sendAjaxRequest('PUT', '/basic/3', ['id' => 3, 'name' => 'Put']);
        $I->seeResponseCodeIs(200);
        $I->see('{"id":3,"name":"Put"}');
    }

    public function delete(AcceptanceTester $I)
    {
        $I->sendAjaxRequest('DELETE', '/basic/3');
        $I->seeResponseCodeIs(204);
    }
}