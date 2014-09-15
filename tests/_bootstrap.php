<?php
// This is global bootstrap for autoloading
require __DIR__.'/../vendor/autoload.php';

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Ovide\Libs\Mvc' => __DIR__.'/../libs/mvc/'
]);
$loader->register();

$app = Ovide\Libs\Mvc\RestApp::instance();

$app->notFound(function() use($app){
    return $app->response->setStatusCode(404, 'Not Found');
});

return $app;
