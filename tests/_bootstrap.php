<?php
// This is global bootstrap for autoloading
require __DIR__.'/../vendor/autoload.php';

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Ovide\Libs\Mvc' => __DIR__.'/../libs/mvc/'
]);
$loader->register();

return Ovide\Libs\Mvc\RestApp::instance();
