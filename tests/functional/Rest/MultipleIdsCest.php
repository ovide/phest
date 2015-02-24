<?php namespace Ovide\Libs\Mvc\Rest;

use Ovide\Libs\Mvc\FunctionalTester;
use Mocks\Controllers\FooVar;

class MultipleIdsCest
{
    public function _before(FunctionalTester $I)
    {
         App::instance()->mountResource(FooVar::class);
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function testGet(FunctionalTester $I)
    {
        $I->sendGET('/foo/1/var');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(FooVar::$data[0]));
    }

    public function testGetOne(FunctionalTester $I)
    {
        $I->sendGET('/foo/1/var/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode(FooVar::$data[0][0]));
    }

    public function testPost(FunctionalTester $I)
    {
        $post = ['id' => 3, 'name' => 'Post', 'description' => 'Post desc'];
        $I->sendPOST('/foo/2/var', $post);
        $I->seeResponseCodeIs(201);
        $I->seeResponseEquals(json_encode($post));
    }

    public function testPut(FunctionalTester $I)
    {
        $put = ['id' => 3, 'name' => 'Put', 'description' => 'Put desc'];
        $I->sendPUT('/foo/2/var/3', $put);
        $I->seeResponseCodeIs(200);
        $I->seeResponseEquals(json_encode($put));
    }

    public function testDelete(FunctionalTester $I)
    {
        $I->sendDELETE('/foo/2/var/3');
        $I->seeResponseCodeIs(204);
    }
}
