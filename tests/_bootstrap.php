<?php
// This is global bootstrap for autoloading
require __DIR__.'/../vendor/autoload.php';

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'ovide\libs\mvc' => __DIR__.'/../libs/mvc/'
]);
$loader->register();

$app = ovide\libs\mvc\RestApp::instance();

$app->notFound(function() use($app){
    $app->response->setStatusCode(404, 'Not Found');
});

return $app;
