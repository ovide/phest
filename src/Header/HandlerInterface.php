<?php namespace Ovide\Phest\Header;

/**
 * 
 * @author Albert Ovide <albert@ovide.net>
 */
interface HandlerInterface
{
    public function before();
    public function after();
    public function finish();
}
