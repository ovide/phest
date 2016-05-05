<?php use Ovide\Libs\Mvc\FunctionalTester;
$I = new FunctionalTester($scenario);

$I->wantTo('test Content-type and Accept headers');


$I->amGoingTo('post a new foo as xml and get the resposne as json');
$I->haveHttpHeader('Content-Type', 'application/xml');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/users', '<?xml version="1.0"?><user><name>foo</name><password>bar</password></user>');
$I->seeResponseCodeIs(201);
$I->seeResponseContainsJson(['id' => 1, 'username' => 'foo', 'password' => 'bar']);
///////////////////////////////
