<?php namespace Ovide\Phest;

use Phalcon\Mvc\Micro\LazyLoader;

/**
 * Description of AclLoader
 *
 * @author Albert Ovide <albert@ovide.net>
 */
class Acl extends \Phalcon\Acl\Adapter\Memory
{
    const AVAILABLE = ['get', 'getOne', 'put', 'patch', 'post', 'delete'];
    
    /**
     * Generates an Acl reading docblocks
     */
    public function reload()
    {
        if ($this->_rolesNames === null) {
            $this->_rolesNames = [];
        }
        $handlers = App::instance()->getHandlers();
        $r = new \ReflectionClass(LazyLoader::class);
        $definition = $r->getProperty('_definition');
        $definition->setAccessible(true);
        $controllers = [];
        foreach ($handlers as $handler) {
            if ((is_array($handler)) && ($handler[1] == 'handle') && ($handler[0] instanceof LazyLoader)){
                $controllers[] = $definition->getValue($handler[0]);
            }
        }
        $regexp = '/@acl +(.+)/';
        foreach ($controllers as $controller) {
            $reflection = new \ReflectionClass($controller);
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $this->addResource($controller::PATH, []);
                $name = $method->getName();
                if (!in_array($name, static::AVAILABLE)) {
                    continue;
                }
                $this->addResourceAccess($controller::PATH, $name);
                /* @var $method  \ReflectionMethod */
                $matches = null;
                if (preg_match($regexp, $method->getDocComment(), $matches)) {
                    $match = explode(',', $matches[1]);
                    array_walk($match, function(&$value){$value = trim($value);});
                    foreach($match as $role) {
                        if (!in_array($role, array_keys($this->_rolesNames))) {
                            $this->addRole($role);
                        }
                        $this->allow($role, $controller::PATH, $name);
                    }   
                }
            }
        }
    }
}

