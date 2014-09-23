<?php namespace Ovide\Libs\Mvc\Rest;

use Phalcon\Mvc\Micro\Collection;
use Phalcon\Mvc\Micro;
use Phalcon\DI\FactoryDefault;

/**
 * Description of RestApp
 * @author Albert Ovide <albert@ovide.net>
 */
class App extends Micro
{
    /**
     * @var Micro
     */
    private static $app;

    public function __construct($dependencyInjector=null) {
        if (self::$app === null) {
            if ($dependencyInjector === null) {
                $dependencyInjector = new FactoryDefault();
            }
            parent::__construct($dependencyInjector);
            self::$app = $this;
            $app = self::$app;
            $app->notFound(function() use($app){
                Controller::notFound($app->response);
                return $app->response;
            });
        } else {
            throw new \RuntimeException("Can't instance RestApp more than once");
        }
    }
    
    /**
     * @return Micro
     */
    public static function instance()
    {
        if (self::$app === null) {
            new App();
        }
        return self::$app;
    }

    /**
     * @param string $route
     * @param string $controller
     * @throws \LogicException
     */
    public static function addResource($route, $controller, $idP='[a-zA-Z0-9_-]*')
    {
        if (is_subclass_of($controller, Controller::class)) {
            $route = trim($route, '/');
            $col   = new Collection();
            $col->setHandler($controller, true);
            $col->setPrefix("/$route");
            $col->map("[/]?{id:$idP}[/]?", 'index');
            self::$app->mount($col);
        } else {
            throw new \LogicException("$controller is not a ".Controller::class);
        }
    }
    
    /**
     * @param array $array []
     *  path => resourceClassName
     */
    public static function addResourceArray($array)
    {
        foreach($array as $path => $class) {
            self::addResource($path, $class);
        }
    }
}
