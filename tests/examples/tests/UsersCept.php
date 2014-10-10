<?php use Ovide\Libs\Mvc\Tester;

$I = new Tester($scenario);
$I->wantTo('Do some CRUD operations with users');

$foo = [
	'username' => 'foo',
	'password' => 'barbarbar',
];

$bar  = [
	'username' => 'bar',
	'password' => 'foofoofoo'
];

$newUser = [
	'username' => $foo['username'],
	'password' => 'bar'
];

$I->amGoingTo('post $newUser '.$foo['username'].' with a short password');

$I->sendPOST('/users', $newUser);
$I->seeHttpHeader('Status', '400 Password is too short');

$I->amGoingTo('set $newUser '.$foo['username'].' with a valid password');
$newUser = [
	'username' => $foo['username'],
	'password' => $foo['password']
];

$I->sendPOST('/users', $newUser);
$I->seeHttpHeader('Status', "201 Created with username {$newUser['username']}");
$I->seeHttpHeader('Location');
$foo['uri'] = $I->grabHttpHeader('Location');

$I->comment('set $newUser with a existent username');
$newUser = [
	'username' => 'foo',
	'password' => $bar['password']
];

$I->sendPOST('/users', $newUser);
$I->seeHttpHeader('Status', "409 User {$newUser['username']} already exists");

$I->comment('set $newUser '.$foo['username']);
$newUser = [
	'username' => $bar['username'],
	'password' => $bar['password']
];

$I->sendPost('/users', $newUser);
$I->seeHttpHeader('Status', "201 Created with username {$newUser['username']}");
$I->seeHttpHeader('Location');
$bar['uri'] = $I->grabHttpHeader('Location');

$I->sendGet($foo['uri']);
$I->seeResponseCodeIs(200);

$I->seeResponseEquals(json_encode([
	'username' => $foo['username'],
	'uri'      => "/users/{$foo['username']}",
	'articles' => "/users/{$foo['username']}/articles"
]));


