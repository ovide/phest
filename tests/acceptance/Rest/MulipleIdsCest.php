<?php
use \AcceptanceTester;
use Ovide\Libs\Mvc\Rest;

class MulipleIdsCest
{
    public function _before(AcceptanceTester $I)
    {
        $app = Rest\App::instance();
        $app->addResource('foo/{fooId:[0-9]*}/var', FooVarMock::class);
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function testGet(AcceptanceTester $I)
    {
        $I->sendGET('/foo/1/var');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(FooVarMock::$data[0]));
    }
    
    public function testGetOne(AcceptanceTester $I)
    {
        $I->sendGET('/foo/1/var/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(FooVarMock::$data[0][0]));
    }
    
    public function testPost(AcceptanceTester $I)
    {
        $post = ['id' => 3, 'name' => 'Post', 'description' => 'Post desc'];
        $I->sendPOST('/foo/2/var', $post);
        $I->seeResponseCodeIs(201);
        $I->seeResponseEquals(json_encode($post));
    }
    
    public function testPut(AcceptanceTester $I)
    {
        $put = ['id' => 3, 'name' => 'Put', 'description' => 'Put desc'];
        $I->sendPUT('/foo/2/var/3', $put);
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode($put));
    }
    
    public function testDelete(AcceptanceTester $I)
    {
        $I->sendDELETE('/foo/2/var/3');
        $I->seeResponseCodeIs(204);
    }
}