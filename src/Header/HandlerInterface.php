<?php namespace Ovide\Libs\Mvc\Rest\Header;

interface HandlerInterface
{
    public function before();
    public function after();
    public function finish();
}
