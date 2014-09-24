phest
=====

Usage example

####index.php
```php
<?php

use Ovide\Libs\Mvc\Rest\App;

require __DIR__.'/../vendor/autoload.php';
$loader->registerNamespaces([
    'My\Controllers\Namespace' => __DIR__.'/dir/to/my/controllers/'
]);
$app = App::instance();

App::addResources([
    'myresource/path'             => MyResource::class,
    'myresource/path/subresource' => SubResource::class,
    'otherResource'               => OtherResource::class
]);

$app->handle();
```


####MyResource.php
```php
<?php namespace My\Controllers\Namespace

class MyResource extends \Ovide\Libs\Mvc\Rest\Controller
{
    public function get()
    {
        //Do your stuff
        return $yourDataArray
    }

    public function getOne($id)
    {
        //...
        return $yourDataArray[$id];
    }

    public function post($obj)
    {
        //Save the object
        return $obj;
    }

    public function put($id, $obj)
    {
        //Update your object
        return $obj;
    }

    public function delete($id)
    {
        //Delete your object
        //No return here
    }
}
```
