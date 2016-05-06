<?php namespace Ovide\Libs\Mvc\Rest\HeaderHandler;

use Phalcon\Events\Event;
use Ovide\Libs\Mvc\Rest\App;
use Ovide\Libs\Mvc\Rest\Exception;
use Ovide\Libs\Mvc\Rest\ContentType;

class Accept extends \Ovide\Libs\Mvc\Rest\Middleware
{
    const HEADER = 'Accept';
    const DEF    = 'application/json';

    /**
     * @var array
     */
    protected $acceptable;
    
    protected $supported;

    protected $contentType;
    protected $accept;
    protected $mediaType;

    public function __construct()
    {
        $this->acceptable = [
            static::DEF => ContentType\Json::class,
        ];
        $this->supported = [
            static::DEF => ContentType\Json::class,
        ];
    }
    
    public function setAcceptable($contentType, $encoder)
    {
        $this->acceptable[$contentType] = $encoder;
    }
    
    public function setSupported($contentType, $decoder)
    {
        $this->supported[$contentType] = $decoder;
    }

    public function beforeExecuteRoute(Event $evt, App $app, $data)
    {
        $this->acceptable($evt, $app);
        
        //Can we parse this Content-Type?
        if (!($this->mediaType = $app->request->getHeader('Content-Type'))) {
            $this->mediaType = static::DEF;
        }
        if (!isset($this->supported[$this->mediaType])) {
            $evt->stop();
            $app->response->setHeader('Accept', implode(', ', array_keys($this->supported)));
            $msg = $this->mediaType.' is not supported by the requested resource';
            throw new Exception\UnsupportedMediaType($msg);
        }
        
        $app->di->set('requestReader', $this->supported[$this->mediaType], true);
    }
    
    protected function acceptable(Event $evt, App $app)
    {
        //Can we generate this content type?
        if (($this->contentType = self::match($this->getHeader('Accept'), array_keys($this->acceptable))) === null) {
            $evt->stop();
            $msg = "Can't generate a '.$this->contentType.' response";
            $this->contentType = static::DEF;
            $this->accept = implode(', ', array_keys($this->acceptable));
            throw new Exception\NotAcceptable($msg);
        }
        
        $app->di->set('responseWriter', $this->acceptable[$this->contentType], true);
    }
    
    /**
     * 
     * @param sting $type
     * @param array $list
     */
    private static function match($type, array $list)
    {
        if (!count($list)) {
            throw new \RuntimeException("No media types available");
        }
        if (!$type) {
            return $list[0];
        }
        
        $types = explode('/', $type);
        if (($types[0] == '*') && ($types[1] != '*')) {
            throw new \RuntimeException("Unmatchable media type: $type");
        }
        
        foreach ($list as $option) {
            $o = explode('/', $option);
            if (($types[0] != '*') && ($o[0] != $types[0])) continue;
            if (($types[1] != '*') && ($o[1] != $types[1])) continue;
            return $option;
        }
        
        return null;
    }
    
    public function beforeException(Event $evt, App $app, $data)
    {
        try {
            $this->acceptable($evt, $app);
        } catch (\Exception $ex) {
            $this->contentType = static::DEF;
            $app->di->set('responseWriter', $this->acceptable[$this->contentType], true);
        }
    }

    public function call(\Phalcon\Mvc\Micro $application)
    {
    }

}
