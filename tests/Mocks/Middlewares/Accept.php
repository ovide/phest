<?php namespace Mocks\Middlewares;

class Accept extends \Igm\Rest\Middleware
{
    const HEADER = 'Accept';

    public function afterExecuteRoute(\Phalcon\Events\Event $evt, \Igm\Rest\App $app, $data)
    {
        if ($accept = $this->getHeader()) {
            switch ($accept) {
                case 'application/xml' :
                    $app->response->setContentType($accept, 'utf-8');
                    $xml = new \SimpleXMLElement('<xml/>');
                    $array = $app->response->getContent();
                    array_walk_recursive($app->response->getContent(), array ($xml, 'addChild'));
                    $app->response->setContent($xml->asXML());
                    break;
                case 'application/json' :
                    static::parseContent($app);
                    break;
            }

        }
    }

    public function beforeExecuteRoute(\Phalcon\Events\Event $evt, \Igm\Rest\App $app, $data)
    {
        $acceptable = ['application/xml', 'application/json'];
        if ($accept = $this->getHeader()) {
            if (!in_array($accept, $acceptable)) {
                $evt->stop();
                throw new \Igm\Rest\Exception\NotAcceptable("Cant generate $accept content");
            }
        }
    }

    public function afterException(\Phalcon\Events\Event $evt, \Igm\Rest\App $app, $data)
    {
        if ($data instanceof \Igm\Rest\Exception\NotAcceptable) {
            static::parseContent($app);
        }
    }

    protected static function parseContent($app)
    {
        $app->response->setContentType('application/json', 'utf-8');
        $app->response->setContent(json_encode($app->response->getContent()));
    }
}
