<?php namespace Mocks\Controllers;

use Ovide\Libs\Mvc\Rest;

class Basic extends Rest\Controller
{
    public function get() {}

    public function getOne($id) {}

    public function post($obj) {}

    public function put($id, $obj) {}

    public function delete($id) {}
}
