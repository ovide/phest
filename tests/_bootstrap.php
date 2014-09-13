<?php
// This is global bootstrap for autoloading
require __DIR__.'/../vendor/autoload.php';

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'ovide\libs\mvc' => __DIR__.'/../libs/mvc/'
]);
$loader->register();

return $app = new ovide\libs\mvc\RestApp();