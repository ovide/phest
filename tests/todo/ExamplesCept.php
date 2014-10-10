<?php 

use Ovide\Libs\Mvc\Rest;
use Mocks\Examples;

$urx = '[a-z]*';
$arx = '[a-z_]*';
$trx = '.*';
$crx = '[0-9]*';

$ur = 'users';
$tr = "$ur/\{$urx}/token";
$ar = "$ur/\{$urx}/articles";
$cr = "$ar/\{$arx}/comments";

$app = Rest\App::instance();
$app->addResource($ur, Examples\User::class, $urx);
$app->addResource($tr, Examples\Token::class, $trx);
$app->addResource($ar, Examples\Article::class, $arx);
$app->addResource($cr, Examples\Comment::class, $crx);
$app->handle();

////////////////////////////////////////////////////////////////////////////////

$I    = new AcceptanceTester($scenario);
$foo  = 'foo';
$fooP = 'barbarbar';


$I->wantTo("Create create a user $foo");

$I->sendPOST('/users', ['username' => $foo, 'password' => 'bar']);
$I->seeResponseCodeIs(Rest\Response::BAD_REQUEST);
$I->seeHttpHeader('Status', '400 Password too short');

$I->sendPOST('/users', ['username' => $foo, 'password' => $fooP]);
$I->seeResponseCodeIs(201);
$I->seeHttpHeader('Location', "/users/$foo");
$I->seeResponseEquals(json_encode([
    'username' => $foo, 'password' => sha1($fooP)
]));

$fooUrl = $I->grabHttpHeader('Location');
$I->wantTo('See the new user');
$I->sendGET($fooUrl);
$I->seeResponseCodeIs(200);
$I->seeResponseEquals(json_encode([
    'username' => $foo,
    'login'    => "/users/$foo/token",
    'articles' => "/users/$foo/articles"
]));

$rsp         = json_decode($I->grabResponse());
$fooLogin    = $rsp->login;
$fooArticles = $rsp->articles;


$I->wantTo("login with $foo");
$I->sendPOST($fooLogin, ['username' => $foo, 'password' => sha1($fooP)]);
$I->seeResponseCodeIs(201);
$rsp = $I->grabResponse();
$fooToken = $rsp['token'];