<?php namespace Ovide\Phest\HeaderHandler;

use Aura\Accept\AcceptFactory;
use Ovide\Phest\App;
use Ovide\Phest\ContentType;
use Ovide\Phest\Exception;
use Ovide\Phest\Middleware;
use Phalcon\Events\Event;

class Accept extends Middleware
{
    const HEADER = 'Accept';
    const DEF    = 'application/json';

    /**
     * @var ContentType\Encoder[] indexed by its CONTENT_TYPE
     */
    protected $acceptable;
    /**
     * @var array
     */
    protected $supported;
    /**
     * @var string
     */
    protected $contentType;
    /**
     * @var string
     */
    protected $accept;
    /**
     * @var string
     */
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
    
    public function setAcceptable(string $encoder, string $contentType='')
    {
        $this->acceptable[$contentType ? $contentType : $encoder::CONTENT_TYPE] = $encoder;
    }
    
    public function setSupported(string $decoder, string $contentType='')
    {
        $this->supported[$contentType ? $contentType : $decoder::CONTENT_TYPE] = $decoder;
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
            $msg = $this->mediaType.' is not supported by the requested resource';
            throw new Exception\UnsupportedMediaType($msg);
        }
        
        $app->di->set('requestReader', $this->supported[$this->mediaType], true);
    }
    
    protected function acceptable(Event $evt, App $app)
    {
        $factory = new AcceptFactory($_SERVER);
        //Can we generate this content type?
        if (!$result = $factory->newInstance()->negotiateMedia(array_keys($this->acceptable))) {
            $evt->stop();
            $this->accept = implode(', ', array_keys($this->acceptable));
            $msg = 'Can\'t generate a response: '.$this->getHeader('Accept');
            $this->contentType = static::DEF;            
            throw new Exception\NotAcceptable($msg);
        }
        $this->contentType = $result->getValue();
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
    
    public function afterException(Event $evt, App $app, $exception)
    {
        if ($exception instanceof Exception\UnsupportedMediaType) {
            $app->response->setHeader('Accept', implode(', ', array_keys($this->supported)));
        }
    }
}
