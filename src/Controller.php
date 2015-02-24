<?php namespace Ovide\Libs\Mvc\Rest;

/**
 * Controller for REST applications
 *
 * Use get() method for GET /resource
 * Use getOne($id) method for GET /resource/{$id}
 * Use put($id, $obj) method for PUT /resource/{$id}
 * Use post($obj) method for POST /resource
 * Use delete($id) method for DELETE /resource/{$id}
 *
 * Just prepend more arguments for more identifiers in the route
 *
 * put($article, $commentId, $content) <= /articles/{article}/comments/{[0-9]*}
 *
 * Use App class for add the controllers to the router
 *
 *
 * @author Albert Ovide <albert@ovide.net>
 */
abstract class Controller extends \Phalcon\Mvc\Controller
{
    /**
     * The resolved method name to call
     *
     * @var string
     */
    protected $_curMethod;

    /**
     * The response represented as an array
     *
     * @var array
     */
    protected $array_response;

    /**
     * Sets if we are on development so we can dump real errors
     *
     * @var bool
     */
    protected static $_devEnv = false;

    /**
     * The key name used to identify unique resources
     */
    const ID   = 'id';

    /**
     * The base path for the resource used as alias
     */
    const PATH = '';

    /**
     * Regular expression that must match with the entity key
     */
    const RX = '[a-zA-Z0-9_-]+';

    public function onConstruct()
    {
        $this->_eventsManager = $this->di->getEventsManager();
        $this->_eventsManager->attach(static::class, $this);
    }

    /**
     * Last argument is matched as the resource main id
     * so must pass an empty string to request a get() or post() call
     *
     * @params string $params List of matched identifiers in the router
     */
    public function handle($arguments = null)
    {
        $params    = func_get_args();

        try {
            $this->_call(func_get_args());
        } catch (\Exception $ex) {
            $this->handleException($ex);
        }

        return $this->response;
    }

    protected function handleException(\Exception $ex)
    {
        //Check if is an internal exception
        //determines if the error message is visible or hidden
        $ix  = ($ex instanceof Exception\Exception);
        $code    = $ix ? $ex->getCode() : Response::INTERNAL_ERROR;
        $message = ($ix || self::$_devEnv) ?
            trim($ex->getMessage()) :
            Response::$status[$code];

        //If $_devEnv is up shows also the debug trace
        $msg     = self::$_devEnv ?
            [
                'message' => trim($ex->getMessage()),
                'code'    => $ex->getCode(),
                'type'    => \get_class($ex),
                'file'    => $ex->getFile(),
                'line'    => $ex->getLine(),
                'trace'   => $ex->getTrace(),
            ]
                :
            [
                'message' => $message,
                'code'    => $code,
            ];
        $this->response = new Response($msg, $code, $message);
    }

    /**
     *
     * @param \Closure $closure
     */
    public static function devEnv(\Closure $closure)
    {
        self::$_devEnv = $closure();
    }

    /**
     * Select the HTTP method to call
     *
     * @param string $method
     * @param string $id
     */
    protected function _call($params)
    {
        $id = array_pop($params);
        switch ($this->request->getMethod()) {
            case 'GET':
                if ($id === '') {
                    $this->_curMethod = 'get';
                    $this->_method(null, $params);
                } else {
                    $this->_curMethod = 'getOne';
                    $this->_method($id, $params);
                }
                break;
            case 'POST':
                $this->_curMethod = 'post';
                $this->_method(null, $params);
                break;
            case 'PUT':
                $this->_curMethod = 'put';
                $this->_method($id, $params);
                break;
            case 'DELETE':
                $this->_curMethod = 'delete';
                $this->_method($id, $params);
                break;
            case 'OPTIONS':
                $this->options();
                break;
            default:
                $this->response = new Response(null, Response::NOT_ALLOWED);
        }
    }

    public function options()
    {
        $options = [];
        $all     = [
            'get'    => 'GET',
            'getOne' => 'GET',
            'put'    => 'PUT',
            'post'   => 'POST',
            'delete' => 'DELETE',
        ];
        $rc = new \ReflectionObject($this);

        /* @var $methods \ReflectionMethod[] */
        $methods = $rc->getMethods();

        $acl = null;

        if ($this->_dependencyInjector->has('acl')) {
            /* @var $acl \Phalcon\Acl\Adapter\Memory */
            $acl      = $this->_dependencyInjector->get('acl');
            $role     = $acl->getActiveRole();
            $resource = $acl->getActiveResource();
        }

        foreach ($methods as $method) {
            $name = $method->getName();
            if (isset($all[$name]) && !in_array($all[$name], $options)) {
                if ($acl === null ||
                    $acl->isAllowed($role, $resource, $all[$name]))
                {
                    $options[] = $all[$name];
                }
            }
        }

        sort($options);

        $list = implode(', ', $options);
        $this->response = new Response('', Response::OK);
        $this->response->setHeader('Allow', $list);
    }

    /**
     *
     * @return array
     * @throws Exception\NotAcceptable
     */
    protected function _getInput()
    {
        $content = $this->request->getRawBody();
        if (!$content) {
            return $this->request->getPost();
        }
        if ($this->request->getServer('CONTENT_TYPE') === 'application/json') {
            $content = json_decode($content, true);
        } else {
            $content = json_decode($content, true);
            if (!is_array($content)) {
                throw new Exception\NotAcceptable();
            }
        }

        return $content;
    }

    /**
     * Internal call to the correct method.
     * Fires a beforeCall and an afterCall event and sets the Response.
     *
     * @param  string                     $id
     * @param  array                      $params
     * @throws Exception\MethodNotAllowed
     */
    protected function _method($id, $params = null)
    {
        $code = null;

        if (!method_exists($this, $this->_curMethod)) {
            throw new Exception\MethodNotAllowed();
        }

        switch ($this->_curMethod) {
            case 'get':
                break;
            case 'post':
                $obj = $this->_getInput();
                array_push($params, $obj);
                break;
            case 'put':
                $obj = $this->_getInput();
                array_push($params, $id, $obj);
                break;
            default:
                array_push($params, $id);
                break;
        }

        $this->_eventsManager->fire(static::class.':beforeCall', $this);
        try {
            $this->array_response = call_user_func_array([$this, $this->_curMethod], $params);
        } catch (\Exception $ex) {
            $this->_eventsManager->fire(static::class.':onErrorCall', $this, $ex);
            throw $ex;
        } finally {
            $this->_eventsManager->fire(static::class.':afterCall', $this);
        }
        $this->_eventsManager->fire(static::class.':onSuccessCall', $this);

        $status   = null;
        $location = null;

        if ($this->_curMethod == 'post') {
            $code = Response::CREATED;
            if (isset($this->array_response[static::ID])) {
                $id       = $this->array_response[static::ID];
                $text     = Response::$status[Response::CREATED];
                $location = rtrim($this->request->getServer('REQUEST_URI'), '/')."/$id";
                $status   = "$text with ".static::ID." $id";
            }
        }

        $this->response = new Response($this->array_response, $code, $status);

        if ($location !== null) {
            $this->response->setHeader('Location', $location);
        }
    }
}
