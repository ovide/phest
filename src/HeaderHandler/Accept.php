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
            'application/json' => ContentType\Json::class,
        ];
        $this->supported = [
            'application/json' => ContentType\Json::class,
        ];
    }

    public function beforeExecuteRoute(Event $evt, App $app, $data)
    {
        $this->acceptable();
        
        //Can we parse this Content-Type?
        if (!($this->mediaType = $app->request->getHeader('Content-Type'))) {
            $this->mediaType = static::DEF;
        }
        if (!isset($this->supported[$this->mediaType])) {
            $evt->stop();
            $this->accept = implode(', ', array_keys($this->supported));
            $msg = $this->mediaType.' is not supported by the requested resource';
            throw new Exception\UnsupportedMediaType($msg);
        }
        
        $app->di->set('requestReader', $this->supported[$this->mediaType], true);
    }
    
    protected function acceptable(Event $evt, App $app)
    {
        //Can we generate this content type?
        if (($this->contentType = $this->getHeader()) && (!isset($this->acceptable[$this->contentType]))) {
            $evt->stop();
            $this->contentType = static::DEF;
            $this->accept = implode(', ', array_keys($this->acceptable));
            throw new Exception\NotAcceptable("Can't generate a '.$this->contentType.' response");
        } elseif (!$this->contentType) {
            $this->contentType = static::DEF;
        }
        
        $app->di->set('responseWriter', $this->acceptable[$this->contentType], true);
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
}
