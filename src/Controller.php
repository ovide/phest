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
 * @example
 * App::addResources(['resource' => MyResource::class]);
 *
 * @author Albert Ovide <albert@ovide.net>
 */
abstract class Controller extends \Phalcon\Mvc\Controller
{
    /**
     * Sets if we are on development so we can dump real errors
     *
     * @var bool
     */
    protected static $_devEnv = false;

    /**
     * The key name used to identify unique resources
     */
    const ID = 'id';

    /**
     * Last argument is matched as the resource main id
     * so must pass an empty string to request a get() or post() call
     *
     * @params string $params List of matched identifiers in the router
     */
    public function _index($arguments=null)
    {
        $params    = func_get_args();

        $method = $this->request->getMethod();
        try {
            $this->_call($method, $params);
        } catch (\Exception $ex) {
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
            $this->response($msg, $code, $message);
        }

        return $this->response;
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
    protected function _call($method, $params)
    {
        $id = array_pop($params);
        switch ($method) {
            case 'GET':
                if ($id === '') {
                    $this->_method('get', null, $params);
                } else {
                    $this->_method('getOne', $id, $params);
                }
                break;
            case 'POST':
                $this->_method('post', null, $params);
                break;
            case 'PUT':
                $this->_method('put', $id, $params);
                break;
            case 'DELETE':
                $this->_method('delete', $id, $params);
                break;
            case 'OPTIONS':
                $this->options();
                break;
            default:
                $this->response(null, Response::NOT_ALLOWED);
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
        $srv = $this->di->getServices();
        if ($this->di->has('acl')) {
            /* @var $acl \Phalcon\Acl\Adapter\Memory */
            $acl      = $this->di->get('acl');
            $role     = $acl->getActiveRole();
            $resource = $acl->getActiveResource();
        }

        foreach ($methods as $method) {
            $name = $method->getName();
            if (isset($all[$name]) && !in_array($all[$name], $options)) {
                if ($acl === null) {
                    $options[] = $all[$name];
                } elseif ($acl->isAllowed($role, $resource, $all[$name])) {
                    $options[] = $all[$name];
                }
            }
        }

        sort($options);

        $list = implode(', ', $options);
        $this->response('', Response::OK);
        $this->response->setHeader('Allow', $list);
    }

    protected function _method($method, $id, $params = null)
    {
        $code = null;

        if (!method_exists($this, $method)) {
            throw new Exception\MethodNotAllowed();
        }

        if ($method == 'getOne' || $method == 'delete') {
            array_unshift($params, $id);
        } elseif ($method == 'post') {
            $obj = $this->request->getPost();
            array_push($params, $obj);
        } elseif ($method == 'put') {
            $obj = $this->request->getPost();
            array_push($params, $id, $obj);
        }

        $rsp      = call_user_func_array([$this, $method], $params);
		$status   = null;
		$location = null;


        if ($method == 'post') {
			$code = Response::CREATED;
			if (isset($rsp[static::ID])) {
				$id       = $rsp[static::ID];
				$location = $this->request->getServer('REQUEST_URI')."/$id";
				$status = Response::$status[Response::CREATED].' with '.static::ID." $id";
			}
        }

		$this->response($rsp, $code, $status);

		if ($location !== null) {
			$this->response->setHeader('Location', $location);
		}

    }

    /**
     * Sets the response content, status code and status message
     * following some basic REST concepts
     *
     * @param string $content The content body
     * @param int    $code    The status code
     * @param string $message The status message
     */
    protected function response($content = null, $code = null, $message = null)
    {
        $this->response = new Response($content, $code, $message);
    }
}
