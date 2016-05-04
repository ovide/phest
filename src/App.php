<?php namespace Ovide\Libs\Mvc\Rest;

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
            $app = self::$app;

            $this->_errorHandler    = function(\Exception $ex) { return $this->errorHandler($ex); };
            $this->_notFoundHandler = function()               { return $this->notFoundHandler(); };
        } else {
            throw new \RuntimeException("Can't instance App more than once");
        }
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

    /**
     * @todo remove me
     * 
     * @param \Phalcon\Mvc\Micro\CollectionInterface $collection
     * @return \Ovide\Libs\Mvc\Rest\App
     * @throws \Exception
     */
	public function mount(Micro\CollectionInterface $collection)
	{
        if (!is_object($collection)) {
			throw new \Exception("Collection is not valid");
		}

		/**
		 * Get the main handler
		 */
		$mainHandler = $collection->getHandler();

		if (empty($mainHandler)) {
			throw new \Exception("Collection requires a main handler");
		}

		$handlers = $collection->getHandlers();
		if (!count($handlers)) {
			throw new \Exception("There are no handlers to mount");
		}

		if (is_array($handlers)) {

			/**
			 * Check if handler is lazy
			 */
			if ($collection->isLazy()) {
				$lazyHandler = new \Phalcon\Mvc\Micro\LazyLoader($mainHandler);
			} else {
				$lazyHandler = $mainHandler;
			}

			/**
			 * Get the main prefix for the collection
			 */
			$prefix = $collection->getPrefix();

            foreach ($handlers as $handler) {

				if (!is_array($handler)) {
					throw new \Exception("One of the registered handlers is invalid");
				}

				$methods    = $handler[0];
				$pattern    = $handler[1];
				$subHandler = $handler[2];
				$name       = $handler[3];

				/**
				 * Create a real handler
				 */
				$realHandler = [$lazyHandler, $subHandler];

				if (!empty($prefix)) {
					if ($pattern == "/") {
						$prefixedPattern = $prefix;
					} else {
						$prefixedPattern = $prefix . $pattern;
					}
				} else {
					$prefixedPattern = $pattern;
				}

				/**
				 * Map the route manually
				 */
				$route = $this->map($prefixedPattern, $realHandler);

				if ((is_string($methods) && ($methods !== '')) || is_array($methods)) {
					$route->via($methods);
				}

				if (is_string($name)) {
					$route->setName($name);
				}
			}
		}

		return $this;
	}
}
