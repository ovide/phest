<?php namespace Ovide\Libs\Mvc\Rest\HeaderHandler;

use Phalcon\Events\Event;
use Ovide\Libs\Mvc\Rest\App;
use Ovide\Libs\Mvc\Rest\Exception;

class Accept extends \Ovide\Libs\Mvc\Rest\Middleware
{
    const HEADER = 'Accept';
    const DEF    = 'application/json';

    /**
     * @var array
     */
    protected $acceptable = [
        'application/json' => 'json'
    ];

    protected $contentType;


    public function afterExecuteRoute(Event $evt, App $app, $data)
    {
        if (!$this->contentType) {
            $this->contentType = static::DEF;
        }
        $this->parseContent($app);
    }

    public function beforeExecuteRoute(Event $evt, App $app, $data)
    {
        if ($accept = $this->getHeader()) {
            $this->contentType = $accept;
        } else {
            $this->contentType = static::DEF;
        }

        if (!isset($this->acceptable[$this->contentType])) {
            $evt->stop();
            $this->contentType = static::DEF;
            throw new Exception\NotAcceptable("Can't generate $accept content");
        }
    }

    public function afterException(Event $evt, App $app, $data)
    {
        $this->parseContent($app);
    }

    protected function parseContent(App $app)
    {
        $func = $this->acceptable[$this->contentType];
        $this->$func($app);
    }

    protected function json(App $app)
    {
        $app->response->setContentType('application/json', 'utf-8');
        $app->response->setContent(json_encode($app->response->getContent()));
    }
}
