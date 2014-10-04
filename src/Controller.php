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
     * The best language accepted for the client.
     * Setted automatically at start from the 'Accept-Language' request header
     * 
     * @var string
     */
    protected $_locale = null;
    protected $_availableLanguages = [];
    protected $_disalowedLanguages = [];
    
    protected $_instanceHeaders = [];

    /**
     * List of registered RequestHeaders to check.
     * 
     * @var Header\RequestHeader[]
     */
    protected static $_globalHeaders = [];
    
    /**
     * Sets if we are on development so we can dump real errors
     * 
     * @var bool
     */
    protected static $_devEnv = false;
    

    /**
     * Last argument is matched as the resource main id
     * so must pass an empty string to request a get() or post() call
     * 
     * @params string $params List of matched identifiers in the router
     */
    public function _index(...$params)
    {
        //$this->_getBestLang();
        //$this->checkHeaders();
                
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
     * 
     * 
     * @todo
     * @param array $classNames
     */
    public static function registerHeaders(Array $classNames)
    {
        foreach($classNames as $className) {
            $rc = new \ReflectionClass($className);
            if ($rc->isSubclassOf(Header\RequestHeader::class)) {
                self::$_globalHeaders[] = $className;
            }
        }
        
    }
    
    protected function checkHeaders()
    {
        $merged = array_unique(
            array_merge(
                static::$_globalHeaders,
                $this->_instanceHeaders
        ));
        $available = array_diff($merged, $this->_disalowedLanguages);
        
        foreach ($available as $header) {
            $register = new $header($this->request);
        }
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
                    $this->_get($params);
                } else {
                    $this->_getOne($id, $params);
                }
                break;
            case 'POST':
                $this->_post($params);
                break;
            case 'PUT':
                $this->_put($id, $params);
                break;
            case 'DELETE':
                $this->_delete($id, $params);
                break;
            default:
                $this->response(null, Response::NOT_ALLOWED);
        }        
    }

    /**
     * Sets the response content, status code and status message
     * following some basic REST concepts
     * 
     * @param string $content The content body
     * @param int $code The status code
     * @param string $message The status message
     */
    protected function response($content=null, $code=null, $message=null)
    {
        $this->response = new Response($content, $code, $message);
    }

    /**
     * GET a single resource
     * 
     * @param string $id
     */
    protected function _getOne($id, $params)
    {
        if (!method_exists($this, 'getOne')) {
            throw new Exception\MethodNotAllowed();
        }
        
        array_unshift($params, $id);
        $rsp = call_user_func_array([$this, 'getOne'], $params);
        
        if ($rsp === false){
            throw new Exception\BadRequest();
        }
        
        $this->response($rsp);
    }

    /**
     * GET a collection resource
     */
    protected function _get($params)
    {
        if (!method_exists($this, 'get')) {
            throw new Exception\MethodNotAllowed();
        }
        $rsp = call_user_func_array([$this, 'get'], $params);
        if ($rsp === false) {
            throw new Exception\BadRequest();
        }
        $this->response($rsp);
    }

    /**
     * POST a new resource to the collection
     */
    protected function _post($params)
    {
        if (!method_exists($this, 'post')) {
            throw new Exception\MethodNotAllowed();
        }
        
        $obj = $this->request->getPost();
        array_push($params, $obj);
        
        $rsp = call_user_func_array([$this, 'post'], $params);
        
        if ($rsp === false) {
            throw new Exception\BadRequest();
        }
        //@todo Insert location of the new resource
        //$this->response->setHeader('Location', '');
        $this->response($rsp, Response::CREATED);
    }

    /**
     * PUT a existent resource updating it
     * 
     * @param string $id
     */
    protected function _put($id, $params)
    {
        if (!method_exists($this, 'put')) {
            throw new Exception\MethodNotAllowed();
        }
        
        $obj = $this->request->getPost();
        array_push($params, $id, $obj);
        
        $rsp = call_user_func_array([$this, 'put'], $params);
        
        if ($rsp === false) {
            throw new Exception\BadRequest();
        }
        $this->response($rsp);
    }

    /**
     * DELETE a resource
     * 
     * @param type $id
     */
    protected function _delete($id, $params)
    {
        if (!method_exists($this, 'delete')) {
            throw new Exception\MethodNotAllowed();
        }
        
        array_unshift($params, $id);
        
        $rsp = call_user_func_array([$this, 'delete'], $params);
        
        if ($rsp === false) {
            throw new Exception\BadRequest();
        }
        
        $this->response(null);
    }
    
    /**
     * Sets $_locale attribute from the Accept-Language request header
     * 
     * @param string[] $moreAvailable
     */
    protected function _getBestLang($moreAvailable=[])
    {
        $merged = array_unique(
            array_merge(
                App::getAvailableLanguages(),
                $this->_availableLanguages,
                $moreAvailable
        ));
        $available  = array_diff($merged, $this->_disalowedLanguages);
        
        $acceptLanguage = new Header\AcceptLanguage($this->request);
        if ($locale = $acceptLanguage->getBestLanguage($available)) {
            $this->_locale = $locale;
        }
    }
    
    /**
     * Disallow an acceptable language for this controller
     * 
     * @param string $lang
     */
    protected function disallowLanguage($lang)
    {
        if (!in_array($lang, $this->_disalowedLanguages))
            $this->_disalowedLanguages[] = $lang;
    }
}
