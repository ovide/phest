<?php namespace Ovide\Libs\Mvc\Rest;

use Phalcon\Mvc\Micro\Collection;
use Phalcon\Mvc\Micro;
use Phalcon\DI\FactoryDefault;

/**
 * Description of App
 * 
 * @author Albert Ovide <albert@ovide.net>
 */
class App extends Micro
{
    /**
     * @var App
     */
    private static $app;
    
    /**
     * @var string[]
     */
    protected $availableLanguages = [];
    
    protected $headerHandlers = [];

    /**
     * 
     * @param FactoryDefault $dependencyInjector
     * @throws \RuntimeException
     */
    public function __construct($dependencyInjector=null) {
        if (self::$app === null) {
            if ($dependencyInjector === null) {
                $dependencyInjector = new FactoryDefault();
            }
            parent::__construct($dependencyInjector);
            self::$app = $this;
            $app = self::$app;
            $app->_notFoundHandler = function() use($app) {
                $response = new Response();
                $response->notFound();
                $app->response = $response;
                return $app->response;
            };
            
            $app->before(function() use ($app) {
                
            });
        } else {
            throw new \RuntimeException("Can't instance RestApp more than once");
        }
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
            $col->map("[/]?{id:$idP}[/]?", '_index');
            static::$app->mount($col);
        } else {
            $msg = "$controller is not a ".Controller::class;
            throw new \LogicException($msg);
        }
    }
    
    /**
     * 
     * @param string $headerHandler Class name of a HeaderHandler
     */
    public static function addHeaderHandler($headerHandler)
    {
        if (is_subclass_of($headerHandler, Header\Handler::class)) {
            $handler = new $headerHandler(self::$app->request);
            self::$app->before(function() use ($handler) {
                $handler->init();
                $handler->handle();
            });
        } else {
            $msg = "$headerHandler is not a ".Header\Handler::class;
            throw new \LogicException($msg);
        }
        
    }
    
    
    
    /**
     * @param string[] $langs
     */
    public static function addLanguages($langs)
    {
        $app = static::instance();
        foreach ($langs as $lang) {
            if (!in_array($lang, $app->availableLanguages)) {
                $app->availableLanguages[] = $lang;
            }
        }
    }
    
    public static function getAvailableLanguages()
    {
        $ins = static::instance();
        return $ins->availableLanguages;
    }
    
    /**
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
