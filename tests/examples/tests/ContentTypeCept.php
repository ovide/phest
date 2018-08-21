<?php
use Ovide\Phest\FunctionalTester;
use Ovide\Phest\ContentType\XmlEncoder;

Ovide\Phest\App::instance()->reset();
$I = new FunctionalTester($scenario);

$handlers = Ovide\Phest\App::instance()->getHandlers();
$accept = $handlers[\Ovide\Phest\HeaderHandler\Accept::HEADER];
$accept->setAcceptable(XmlEncoder::class);

$I->wantTo('test Content-type and Accept headers');


$I->amGoingTo('post a new foo as xml and expect the response as json: error 415');
$I->haveHttpHeader('Content-Type', 'application/xml');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendPOST('/users', '<?xml version="1.0"?><user><name>foo</name><password>bar</password></user>');
$I->seeResponseCodeIs(415);
$I->seeHttpHeader('Accept', 'application/json');
///////////////////////////////
$I->amGoingTo('post a new foo as json and get the response as xml');
$I->haveHttpHeader('Content-Type', 'application/json');
$I->haveHttpHeader('Accept', 'application/xml');
$I->sendPOST('/users', json_encode(['username' => 'fooo', 'password' => 'barbarbar']));
$I->seeResponseCodeIs(201);
$I->seeResponseContains(
    '<?xml version="1.0"?>'.PHP_EOL.
    '<root><username>fooo</username><uri>/users/fooo</uri><articles>/users/fooo/articles</articles></root>'
);


//Clean
Mocks\Examples\User::$data = [];