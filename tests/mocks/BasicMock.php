<?php


/**
 * Description of BasicMock
 * @author Albert Ovide <albert@ovide.net>
 */
class BasicMock extends \Ovide\Libs\Mvc\RestController
{
    private static $data = [
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

    public function put($obj)
    {
        return $obj;
    }

    public function delete($id)
    {
    }
}

