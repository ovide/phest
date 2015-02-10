<?php namespace Ovide\Libs\Mvc\Rest;

class Router extends \Phalcon\Mvc\Router implements \Serializable
{
    public function serialize()
    {
        $di = $this->_dependencyInjector;
        $this->_dependencyInjector = null;

        $data = array();
        $vars = get_object_vars($this);
        foreach (array_keys($vars) as $var) {
            $data[$var] = $this->$var;
        }

        $this->_dependencyInjector = $di;

        return serialize($data);
    }

    public function unserialize($serialized)
    {
        $this->_dependencyInjector = \Phalcon\DI::getDefault();

        $data = unserialize($serialized);
        foreach ($data as $key => $value) {
            if ($key !== '_dependencyInjector') {
                $this->$key = $value;
            }
        }
    }
}
