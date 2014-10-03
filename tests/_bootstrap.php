<?php
// This is global bootstrap for autoloading
require __DIR__.'/../vendor/autoload.php';

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Ovide\Libs\Mvc\Rest'           => __DIR__.'/../Libs/Mvc/Rest/',
    'Ovide\Libs\Mvc\Rest\Exception' => __DIR__.'/../Libs/Mvc/Rest/Exception/',
    'Mocks\Controllers'             => __DIR__.'/Mocks/Controllers/',
    'Mocks\Headers'                 => __DIR__.'/Mocks/Headers/',
]);
$loader->register();
