<?php namespace Ovide\Phest;

use Phalcon\Mvc\Micro\Collection;
use Phalcon\Mvc\Micro;
use Phalcon\DI\FactoryDefault;

/**
 * @property Response $response
 * @author Albert Ovide <albert@ovide.net>
 */
class App extends Micro
{
    /**
     * @var App Singleton instance
     */
    protected static $app;

    /**
     * Sets if we are on development so we can dump real errors.
     *
     * @var boolean
     */
    public $devEnv = false;
    
    protected $_config = [];

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
            $this->setEventsManager($dependencyInjector->getShared('eventsManager'));
            $this->addHeaderHandler(new HeaderHandler\Accept());
            $app = self::$app;

            $this->_errorHandler    = function(\Exception $ex) { return $this->errorHandler($ex); };
            $this->_notFoundHandler = function()               { return $this->notFoundHandler(); };
        } else {
            throw new \RuntimeException("Can't instance App more than once");
        }
    }
    
    public static function reset()
    {
        self::$app = null;
    }

    protected function notFoundHandler()
    {
        $this->response->notFound();
        $this->_eventsManager->fire('micro:afterExecuteRoute', $this);
        return $this->response;
    }

    public function errorHandler(\Exception $ex)
    {
        $ix  = ($ex instanceof Exception\Exception);
        $code    = $ix ? $ex->getCode() : Response::INTERNAL_ERROR;
        $message = ($ix || $this->devEnv) ?
            trim($ex->getMessage()) :
            Response::$status[$code];

        //If $_devEnv is up shows also the debug trace
        $msg     = $this->devEnv ?
            [
                'message' => trim($ex->getMessage()),
                'code'    => $ex->getCode(),
                'type'    => \get_class($ex),
                'file'    => $ex->getFile(),
                'line'    => $ex->getLine(),
                'trace'   => $ex->getTrace(),
            ]:[
                'message' => $message,
                'code'    => $code,
            ];
        $this->response->rebuild($msg, $code, $message);
        $this->response->encodeContent();

        /* @var $evt \Phalcon\Events\Manager */
        $this->_eventsManager->fire('micro:afterException', $this, $ex);

        return $this->response;
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
        if (!is_subclass_of($controller, Controller::class)) {
            throw new \RuntimeException($controller.' is not a '.Controller::class);
        }
        $rx  = $controller::RX;

        $col = new Collection();
        $col->setHandler($controller, true);
        $col->setPrefix($controller::PATH);
        $col->map("[/]?{id:$}", 'handle', $controller::PATH);
        $col->map("/{id:($rx)}[/]?", 'handle', $controller::PATH);
        $this->mount($col);
    }

    /**
     * Adds a HeaderHandler to the application.
     *
     * @param string $headerHandler Class name of a HeaderHandler
     */
    public static function addHeaderHandler($headerHandler)
    {
        if (isset(static::$app->_handlers[$headerHandler::HEADER])) {
            static::$app->_eventsManager->detach('micro', static::$app->_handlers[$headerHandler::HEADER]);
        }
        static::$app->_handlers[$headerHandler::HEADER] = $headerHandler;
        static::$app->_eventsManager->attach('micro', $headerHandler);
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
    
    
    public function setConfig($handler, $key, $value)
    {
        if (!isset($this->_config[$handler])) {
            $this->_config[$handler] = [];
        }
        
        $this->_config[$handler][$key] = $value;
    }
    
    public function getConfig($handler, $key)
    {
        if (isset($this->_config[$handler][$key])) {
            return $this->_config[$handler][$key];
        } else {
            throw new \RuntimeException("'$key' value not found for '$handler'");
        }
    }
}
