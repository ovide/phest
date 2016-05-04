<?php namespace Mocks\Controllers;

use Ovide\Libs\Mvc\Rest;

class FooVar extends Rest\Controller
{
    public static $data = [
        [
            [
                'id'          => 1,
                'name'        => 'foo1',
                'description' => 'foo1 desc'
            ],
            [
                'id'          => 2,
                'name'        => 'foo2',
                'description' => 'foo2 desc'
            ]
        ],
        [
            [
                'id'          => 1,
                'name'        => 'var1',
                'description' => 'var1 desc'
            ],
            [
                'id'          => 2,
                'name'        => 'var2',
                'description' => 'var2 desc'
            ]
        ]
    ];

    public function get($fooId)
    {
        return self::$data[$fooId-1];
    }

    public function getOne($fooId, $varId)
    {
        if (!isset(self::$data[$fooId-1][$varId - 1])) {
            throw new Rest\Exception\NotFound("$varId not found");
        }
        return self::$data[$fooId-1][$varId-1];
    }

    public function post($fooId, $varObj)
    {
        $new = [
            'id'          => count(self::$data[$fooId-1]) + 1,
            'name'        => $varObj['name'],
            'description' => $varObj['description']
        ];
        self::$data[$fooId-1][] = $new;
        return $new;
    }

    public function delete($fooId, $varId)
    {
        if ($fooId < 1)
            throw new Rest\Exception\InternalServerError();

        if ($varId < 3)
            throw new Rest\Exception\Forbidden('I need that resource for testing');

        unset(self::$data[$fooId-1][$varId-1]);
    }

    public function put($fooId, $varId, $varObj)
    {
        if (!isset($varObj['name']) || !isset($varObj['description']))
            throw new Rest\Exception\BadRequest();

        $obj = [
            'id'          => (int)$varObj['id'],
            'name'        => $varObj['name'],
            'description' => $varObj['description']
        ];

        self::$data[$fooId-1][$varId - 1] = $obj;
        return $obj;
    }
}
