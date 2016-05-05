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
        $this->_eventsManager = $this->di->get('eventsManager');
		$this->_eventsManager->attach(static::class, $this);
    }

    /**
     * Select the HTTP method to call
     *
     * Last argument is matched as the resource main id
     * so must pass an empty string to request a get() or post() call
     */
    public function handle()
    {
        $params = func_get_args();
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
                throw new Exception\MethodNotAllowed();
        }

        return $this->response;
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
        $this->response->rebuild('', Response::OK);
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
        
        /* @var $reader ContentType\Decoder */
        $reader = $this->di->get('requestReader');
        return $reader->decode($content);
	}

    /**
     * Internal call to the correct method.
     * Fires a beforeCall and an afterCall event and sets the Response.
     *
     * @param string $id
     * @param array $params
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

        $this->response->rebuild($this->array_response, $code, $status);

        if ($location !== null) {
            $this->response->setHeader('Location', $location);
        }
    }
}
