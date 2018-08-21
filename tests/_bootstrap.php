<?php require __DIR__.'/../vendor/autoload.php';

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Mocks\Controllers'         => __DIR__.'/Mocks/Controllers/',
    'Mocks\Headers'             => __DIR__.'/Mocks/Headers/',
    'Mocks\Examples'            => __DIR__.'/Mocks/Examples/',
    'Mocks\Middlewares'         => __DIR__.'/Mocks/Middlewares/',
]);
$loader->register();

set_error_handler(function($errNumber, $errStr, $errFile, $errLine) {
    throw new \ErrorException($errStr, 0, $errNumber, $errFile, $errLine);
});
