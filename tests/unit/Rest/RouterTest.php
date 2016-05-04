<?php


class RouterTest extends \Codeception\TestCase\Test
{
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSerialization()
    {
        $router = \Phalcon\DI::getDefault()->getShared('router');
        $serialized = serialize($router);
        $unserialized = unserialize($serialized);
        $this->assertEquals($unserialized, $router);
    }
}