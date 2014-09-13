<?php namespace ovide\libs\mvc;

use Phalcon\Mvc\Micro\Collection;
use Phalcon\Mvc\Micro;
use Phalcon\DI;

/**
 * Description of RestApp
 * @author Albert Ovide <albert@ovide.net>
 */
class RestApp extends Micro
{
    public function __construct($dependencyInjector = null)
    {
        if (!$dependencyInjector)
            $dependencyInjector = DI::getDefault();
        parent::__construct($dependencyInjector);
    }

    public function addResource($route, $controller)
    {
        $col = new Collection();
        $col->setHandler($controller, true);
        $col->setPrefix($route);
        $col->map('/(id)?', 'index');
        $this->mount($col);
    }
}

