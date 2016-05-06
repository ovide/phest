<?php
use Mocks\Examples\User;
use Ovide\Libs\Mvc\Rest\App;
use Phalcon\Acl;
use Ovide\Libs\Mvc\Rest\ContentType\XmlEncoder;

App::reset();
$app = App::instance();
$handlers = $app->getHandlers();
$accept = $handlers[\Ovide\Libs\Mvc\Rest\HeaderHandler\Accept::HEADER];
$accept->setAcceptable(XmlEncoder::CONTENT_TYPE, XmlEncoder::class);
$app->mountResource(User::class);

$app->di->set('acl', function() {
	$guest = new Acl\Role('guest');
	$user  = new Acl\Role('user');
	$root  = new Acl\Role('root');

	$users = new Acl\Resource('users');

	$acl = new Acl\Adapter\Memory();
	$acl->addRole($guest);
	$acl->addRole($user, $guest);
	$acl->addRole($root, $user);
	$acl->addResource($users, ['delete', 'get', 'getOne', 'post', 'put', 'putSelf', 'getSelf', 'deleteSelf']);
	$acl->allow('guest', 'users', ['post']);
	$acl->allow('user', 'users', ['getSelf', 'deleteSelf', 'putSelf']);
	$acl->deny('user', 'users', 'post');
	$acl->allow('root', 'users', '*');
	$acl->setDefaultAction(Acl::DENY);
	//Sets 'gest' as active role
	$acl->isAllowed('guest', '', '');
	return $acl;
}, true);


return $app;
