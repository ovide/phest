<?php

use Ovide\Libs\Mvc\Rest;

/**
 * Description of BasicMock
 * @author Albert Ovide <albert@ovide.net>
 */
class BasicMock extends Rest\Controller
{
    public static $data = [
        [
            'id' => 1,
            'name' => 'Foo'
        ],
        [
            'id' => 2,
            'name' => 'Var'
        ]
    ];

    public function get()
    {
        return self::$data;
    }

    public function getOne($id)
    {
        return self::$data[$id-1];
    }

    public function post($obj)
    {
        return $obj;
    }

    public function put($id, $obj)
    {
        return $obj;
    }

    public function delete($id)
    {
    }
}

