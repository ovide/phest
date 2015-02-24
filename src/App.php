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
     * @param  FactoryDefault    $dependencyInjector
     * @throws \RuntimeException
     */
    public function __construct($dependencyInjector = null)
    {
        if (self::$app === null) {
            if ($dependencyInjector === null) {
                $dependencyInjector = new FactoryDefault();
            }
            $dependencyInjector->setShared('response', Response::class);
            $dependencyInjector->setShared('router'  , Router::class);
            parent::__construct($dependencyInjector);
            self::$app = $this;
            $app = self::$app;

            $app->_notFoundHandler = function () use ($app) {
                $app->response->notFound();

                return $app->response;
            };
        } else {
            throw new \RuntimeException("Can't instance App more than once");
        }
    }

    /**
     * Sets a config value for a set-key pair
     *
     * @param string $set
     * @param mixed  $key
     * @param mixed  $value
     */
    public function setConfig($set, $key, $value)
    {
        $this->_config[$set][$key] = $value;
    }

    /**
     * Returns the config value for a set-key pair
     *
     * @param  string $set
     * @param  mixed  $key
     * @return mixed
     */
    public function getConfig($set, $key)
    {
        return isset($this->_config[$set][$key]) ?
            $this->_config[$set][$key] :
            null;
    }

    /**
     * Adds new resources to the app
     *
     * @see addResource()
     * @param array $resources resource class Names
     */
    public function addResources(array $resources)
    {
        /* @var $di \Phalcon\DI */
        $di    = $this->_dependencyInjector;
        $cache = $cached = null;
        if ($di->has('cache')) {
            $cache  = $di->getShared('cache');
            $key    = "router";
            $cached = $cache->get($key);
        }
        if (!$cached) {
            foreach ($resources as $resource) {
                $this->mountResource($resource);
            }

            if ($cache !== null) {
                $cache->save($key, ['router' => $this->_router, 'handlers' => $this->_handlers]);
            }
        } else {
            $this->_handlers = $cached['handlers'];
            $this->_router   = $cached['router'];
            $this->_dependencyInjector->setShared('router', $cached['router']);
        }
    }

    /**
     * Adds a new resource to the app
     *
     * @param  string $controller The name of the controller class.
     */
    public function mountResource($controller)
    {
        $rx  = $controller::RX;
        $col = new Collection();
        $col->setHandler($controller, true);
        $col->setPrefix($controller::PATH);
        $col->map("[/]?{id:$}", 'handle', $controller::PATH);
        $col->map("/{id:$rx}[/]?", 'handle', $controller::PATH);
        $this->mount($col);
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
            self::$app->before(function () use ($handler) {
                $handler->init();
                if ($handler->get()) {
                    $handler->before();
                }
            });
            self::$app->after(function () use ($handler) {
                if ($handler->get()) {
                    $handler->after();
                }
            });
            self::$app->finish(function () use ($handler) {
                if ($handler->get()) {
                    $handler->finish();
                }
            });
        } else {
            $msg = "$headerHandler is not a ".Header\Handler::class;
            throw new \LogicException($msg);
        }
    }

    /**
     * @return App
     */
    final public static function instance()
    {
        if (self::$app === null) {
            $class = static::class;
            new $class();
        }

        return self::$app;
    }
}
