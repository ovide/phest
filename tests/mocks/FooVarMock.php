<?php

use Ovide\Libs\Mvc\Rest;

class FooVarMock extends Rest\Controller
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
        self::$data[$fooId-1][] = [
            'id'          => count(self::$data[$fooId-1]) + 1,
            'name'        => $varObj['name'],
            'description' => $varObj['description']
        ];
        return $varObj;
    }
    
    public function delete($fooId, $varId)
    {
        if ($varId < 1)
            throw new Rest\Exception\InternalServerError();
        
        if ($fooId < 3)
            throw new Rest\Exception\Forbidden('I need that resource for testing');
        
        unset(self::$data[$fooId-1][$varId-1]);
    }
    
    public function put($fooId, $varId, $varObj)
    {
        if (!isset($varObj['name']) || !isset($varObj['description']))
            throw new Rest\Exception\BadRequest();
        
        self::$data[$fooId-1][$varId - 1] = [
            'id'          => $varId - 1,
            'name'        => $varObj['name'],
            'description' => $varObj['description']
        ];
        return $varObj;
    }
}
