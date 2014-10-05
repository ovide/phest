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
     * @var App Singleton instance
     */
    private static $app;
    
    /**
     * @var string[]
     */
    protected $availableLanguages = [];

    /**
     * Constructs the app.
     * 
     * <ul>
     * <li>Checks singleton instance</li>
     * <li>Adds a dependency injector if none provided</li>
     * <li>Sets the notFound handler</li>
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
            $app->notFound(function() use($app){
                $response = new Response();
                $response->notFound();
                $app->response = $response;
                return $app->response;
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
            throw new \LogicException("$controller is not a ".Controller::class);
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
