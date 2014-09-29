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
     * @var string
     */
    protected $_locale = 'en';
    protected $_availableLanguages = [];
    protected $_disalowedLanguages = [];

    public function _index(...$params)
    {
        $this->getBestLang();
        $method = $this->request->getMethod();
        try {
            $this->_call($method, $params);
        } catch (\Exception $ex) {
            $this->response([
                'message' => $ex->getMessage(),
                'code'    => $ex->getCode(),
                //'type'    => \get_class($ex),
                //'file'    => $ex->getFile(),
                //'line'    => $ex->getLine()
            ], ($ex instanceof Exception\Exception) ? $ex->getCode() : Response::INTERNAL_ERROR);
        }
        return $this->response;
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
        array_unshift($params, $id);
        $this->response(call_user_func_array([$this, 'getOne'], $params));
    }

    /**
     * GET a collection resource
     */
    protected function _get($params)
    {
        $this->response(call_user_func_array([$this, 'get'], $params));
    }

    /**
     * POST a new resource to the collection
     */
    protected function _post($params)
    {
        $obj = $this->request->getPost();
        array_push($params, $obj);
        $this->response(call_user_func_array([$this, 'post'], $params), Response::CREATED);
    }

    /**
     * PUT a existent resource updating it
     * 
     * @param string $id
     */
    protected function _put($id, $params)
    {
        
        $obj = $this->request->getPost();
        array_push($params, $id, $obj);
        $this->response(call_user_func_array([$this, 'put'], $params));
    }

    /**
     * DELETE a resource
     * 
     * @param type $id
     */
    protected function _delete($id, $params)
    {
        array_unshift($params, $id);
        call_user_func_array([$this, 'delete'], $params);
        $this->response(null);
    }
    
    /**
     * Sets $_locale attribute from the Accept-Language request header
     * 
     * @param string[] $moreAvailable
     */
    protected function getBestLang($moreAvailable=[])
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
