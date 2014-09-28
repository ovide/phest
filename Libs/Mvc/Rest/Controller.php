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

    public function _index($id='')
    {
        $this->getBestLang();
        $method = $this->request->getMethod();
        try {
            $this->_call($method, $id);
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
    protected function _call($method, $id)
    {
        switch ($method) {
            case 'GET':
                if ($id === '') {
                    $this->_get();
                } else {
                    $this->_getOne($id);
                }
                break;
            case 'POST':
                $this->_post();
                break;
            case 'PUT':
                $this->_put($id);
                break;
            case 'DELETE':
                $this->_delete($id);
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
    protected function _getOne($id)
    {
        $this->response($this->getOne($id));
    }

    /**
     * GET a collection resource
     */
    protected function _get()
    {
        $this->response($this->get());
    }

    /**
     * POST a new resource to the collection
     */
    protected function _post()
    {
        $obj = $this->request->getPost();
        $this->response($this->post($obj), Response::CREATED);
    }

    /**
     * PUT a existent resource updating it
     * 
     * @param string $id
     */
    protected function _put($id)
    {
        $obj = $this->request->getPost();
        $this->response($this->put($id, $obj));
    }

    /**
     * DELETE a resource
     * 
     * @param type $id
     */
    protected function _delete($id)
    {
        $this->delete($id);
        $this->response(null);
    }
    
    /**
     * Sets $_locale attribute from the Accept-Language request header
     * 
     * @param string[] $moreAvailable
     */
    protected function getBestLang($moreAvailable=[])
    {
        $merged = array_unique(array_merge(App::getAvailableLanguages(),
                                 $this->_availableLanguages,
                                 $moreAvailable));
        $available = array_diff($merged, $this->_disalowedLanguages);
        $acceptable = $this->request->getHeader('HTTP_ACCEPT_LANGUAGE');
        $accArr     = self::parseAcceptableLanguages($acceptable);
        $match      = false;
                
        while (($lang = key($accArr)) && (!$match)) {
            next($accArr);
            $locale = \Locale::lookup($available, $lang, true);
            if ($locale) {
                $this->_locale = $locale;
                $match = true;
            }
        }
        return $this->_locale;
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

    /**
     * Parse the Accept-Language header
     * 
     * ca,es;q=0.7,en;q=0.3
     * 
     * returns an array ordered by 'q'
     * 
     * ca => 1
     * es => 0.7
     * en => 0.3
     * 
     * @param string $acceptable
     * @return array string => float
     */
    private final static function parseAcceptableLanguages($acceptable)
    {
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',$acceptable, $lang_parse);
        if (count($lang_parse[1])) {
            $langs = array_combine($lang_parse[1], $lang_parse[4]);
            foreach ($langs as $lang => $val) {
                if ($val === '') {
                    $langs[$lang] = 1;
                }
            }
            arsort($langs, SORT_NUMERIC);
            return $langs;
        }
        return [];
    }
}
