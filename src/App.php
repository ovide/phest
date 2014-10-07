<?php namespace Ovide\Libs\Mvc\Rest;

use Phalcon\Mvc\Micro\Collection;
use Phalcon\Mvc\Micro;
use Phalcon\DI\FactoryDefault;

/**
 * 
 * @author Albert Ovide <albert@ovide.net>
 */
class App extends Micro
{
    /**
     * @var App Singleton instance
     */
    private static $app;

    /**
     * Configuration sets for HeaderHandlers
     * @var Array
     */
    private $_config = [];
    
    /**
     * Constructs the app.
     * 
     * Checks singleton instance
     * Adds a dependency injector if none provided
     * Sets the notFound handler
     * 
     * @param FactoryDefault $dependencyInjector
     * @throws \RuntimeException
     */
    public function __construct($dependencyInjector=null)
    {
        if (self::$app === null) {
            
            if ($dependencyInjector === null) {
                $dependencyInjector = new FactoryDefault();
            }
            $dependencyInjector->setShared('response', function() {
                return new Response();
            });
            parent::__construct($dependencyInjector);
            self::$app = $this;
            $app = self::$app;
            
            $app->_notFoundHandler = function() use($app) {
                $app->response->notFound();
                return $app->response;
            };
            
        } else {
            throw new \RuntimeException("Can't instance RestApp more than once");
        }
    }
    
    /**
     * Sets a config value for a set-key pair
     * 
     * @param string $set
     * @param mixed $key
     * @param mixed $value
     */
    public function setConfig($set, $key, $value)
    {
        $this->_config[$set][$key] = $value;
    }
    
    /**
     * Returns the config value for a set-key pair
     * 
     * @param string $set
     * @param mixed $key
     * @return mixed
     */
    public function getConfig($set, $key)
    {
        return isset($this->_config[$set][$key]) ?
            $this->_config[$set][$key] :
            null;
    }
  
    /**
     * @return App
     */
    public final static function instance()
    {
        if (self::$app === null) {
            $class = static::class;
            new $class();
        }
        return self::$app;
    }

    /**
     * Adds a new resource to the app
     * 
     * @param string $route
     * The route associated to the resource.
     * Allows regular expressions and wildcards
     * @param string $controller
     * The name of the controller class. Must interhit from Controller
     * @param string $idP
     * The regular expression for the main resource identifier.
     * Used as $id in the controller method.
     * @throws \LogicException
     * If the controller class name provided is not a Controller
     * @example App::addResource('/foo/bar', Foo::class);
     * @example App::addResource('/foo/{fooId}/bar', Foo::class);
     * @example App::addResource('/foo/{[0-9]*}/bar', Foo::class, '[a-z]*');
     */
    public static function addResource($route, $controller, $idP='[a-zA-Z0-9_-]*')
    {
        if (is_subclass_of($controller, Controller::class)) {
            $route = trim($route, '/');
            $col   = new Collection();
            $col->setHandler($controller, true);
            $col->setPrefix("/$route");
            $col->map("[/]?{id:$idP}[/]?", '_index');
            static::$app->mount($col);
        } else {
            $msg = "$controller is not a ".Controller::class;
            throw new \LogicException($msg);
        }
    }
    
    /**
     * Adds a HeaderHandler to the application.
     * 
     * @param string $headerHandler Class name of a HeaderHandler
     */
    public static function addHeaderHandler($headerHandler)
    {
        if (is_subclass_of($headerHandler, Header\Handler::class)) {
            $handler = new $headerHandler(self::$app->request);
            self::$app->before(function() use ($handler) {
                $handler->init();
                if ($handler->get()) {
                    $handler->handle();
                }
            });
        } else {
            $msg = "$headerHandler is not a ".Header\Handler::class;
            throw new \LogicException($msg);
        }
        
    }
    
    /**
     * Adds new resources to the app
     * 
     * @see addResource()
     * @param array $array []
     *  path => resourceClassName
     */
    public static function addResources($array)
    {
        $i = static::instance();
        foreach($array as $path => $class) {
            $i->addResource($path, $class);
        }
    }
}
