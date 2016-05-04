<?php namespace Mocks\Controllers;

use Ovide\Libs\Mvc\Rest;

class Foo extends Rest\Controller
{
    public static $data = [
        [
            'id'          => 1,
            'name'        => 'foo',
            'description' => 'foo desc'
        ],
        [
            'id'          => 2,
            'name'        => 'var',
            'description' => 'var desc'
        ]
    ];

    public function get()
    {
        return self::$data;
    }

    public function getOne($id)
    {
        if (!is_numeric($id)) {
            throw new \Exception('Foo exception', 555);
        }
        if (!isset(self::$data[$id - 1])) {
            throw new Rest\Exception\NotFound("$id not found");
        }
        return self::$data[$id-1];
    }

    public function post($obj)
    {
        self::$data[] = [
            'id'          => count(self::$data) + 1,
            'name'        => $obj['name'],
            'description' => $obj['description']
        ];

        return self::$data[count(self::$data) - 1];
    }

    public function delete($id)
    {
        if ($id < 1)
            throw new Rest\Exception\InternalServerError();

        if ($id == 1 || $id == 2)
            throw new Rest\Exception\Forbidden('I need that resource for testing');

        unset(self::$data[$id-1]);
    }

    public function put($id, $obj)
    {
        if (!isset($obj['name']) || !isset($obj['description']))
            throw new Rest\Exception\BadRequest();

        self::$data[$id - 1] = [
            'id'          => $id - 1,
            'name'        => $obj['name'],
            'description' => $obj['description']
        ];
    }
}
