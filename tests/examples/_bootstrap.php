<?php use Mocks\Examples\User;

$app = Ovide\Libs\Mvc\Rest\App::instance();
$app->addResource(User::PATH, User::class, User::RX);

return $app;
\Codeception\Util\Autoload::registerSuffix('Page', __DIR__.DIRECTORY_SEPARATOR.'_pages');
\Codeception\Util\Autoload::registerSuffix('Steps', __DIR__.DIRECTORY_SEPARATOR.'_steps');