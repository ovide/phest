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

///////////////////////////////
$I->amGoingTo('post $newUser '.$foo['username'].' with a short password');

$I->sendPOST('/users', $newUser);
$I->seeHttpHeader('Status', '400 Password is too short');

///////////////////////////////
$I->amGoingTo('post $newUser '.$foo['username'].' with a valid password');
$newUser = [
	'username' => $foo['username'],
	'password' => $foo['password']
];

$I->sendPOST('/users', $newUser);
$I->seeHttpHeader('Status', "201 Created with username {$newUser['username']}");
$I->seeHttpHeader('Location');
$foo['uri'] = $I->grabHttpHeader('Location');

///////////////////////////////
$I->amGoingTo('post $newUser with a existent username');
$newUser = [
	'username' => 'foo',
	'password' => $bar['password']
];

$I->sendPOST('/users', $newUser);
$I->seeHttpHeader('Status', "409 User {$newUser['username']} already exists");

///////////////////////////////
$I->amGoingTo('post $newUser '.$foo['username']);
$newUser = [
	'username' => $bar['username'],
	'password' => $bar['password']
];

$I->sendPost('/users', $newUser);
$I->seeHttpHeader('Status', "201 Created with username {$newUser['username']}");
$I->seeHttpHeader('Location');
$bar['uri'] = $I->grabHttpHeader('Location');

///////////////////////////////
$I->amGoingTo('request user $foo at '.$foo['uri']);
$I->sendGet($foo['uri']);
$I->seeResponseCodeIs(200);

$I->seeResponseEquals(json_encode([
	'username' => $foo['username'],
	'uri'      => "/users/{$foo['username']}",
	'articles' => "/users/{$foo['username']}/articles"
]));

///////////////////////////////
$I->amGoingTo('request all users');
$I->sendGet('/users');
$I->seeResponseCodeIs(200);
$I->seeResponseEquals(json_encode([
	[
		'username' => $foo['username'],
		'uri'      => "/users/{$foo['username']}",
		'articles' => "/users/{$foo['username']}/articles"
	],
	[
		'username' => $bar['username'],
		'uri'      => "/users/{$bar['username']}",
		'articles' => "/users/{$bar['username']}/articles"
	]
]));
