<?php namespace Mocks\Controllers;

use Ovide\Phest\Controller;

class Basic extends Controller
{
    public function get() {}

    public function getOne($id) {}

    public function post($obj) {}

    public function put($id, $obj) {}

    public function delete($id) {}
}
