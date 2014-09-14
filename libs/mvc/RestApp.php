<?php namespace Ovide\Libs\Mvc;

use Phalcon\Mvc\Micro\Collection;
use Phalcon\Mvc\Micro;
use Phalcon\DI;

/**
 * Description of RestApp
 * @author Albert Ovide <albert@ovide.net>
 */
class RestApp extends Micro
{
    /**
     * @var Micro
     */
    private static $app;

    public function __construct($dependencyInjector = null)
    {
        if (!$dependencyInjector)
            $dependencyInjector = DI::getDefault();
        parent::__construct($dependencyInjector);
    }

    /**
     * @return Micro
     */
    public static function instance()
    {
        if (self::$app === null) {
            self::$app = new RestApp();
        }
        return self::$app;
    }

    /**
     * @param string $route
     * @param string $controller
     * @throws \LogicException
     */
    public static function addResource($route, $controller, $idP='[a-zA-Z0-9_-]+')
    {
        if (is_subclass_of($controller, RestController::class)) {
            $route = trim($route, '/');
            $col   = new Collection();
            $col->setHandler($controller, true);
            $col->setPrefix("/$route");
            $col->map("(/$idP)?", 'index');
            self::$app->mount($col);
        } else {
            throw new \LogicException("$controller is not a ".RestController::class);
        }
    }
}
