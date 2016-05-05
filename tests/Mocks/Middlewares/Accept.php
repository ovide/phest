<?php namespace Mocks\Middlewares;

use Ovide\Libs\Mvc\Rest;

class Accept extends Rest\HeaderHandler\Accept
{
    protected $acceptable = [
        'application/json' => 'json',
        'application/xml'  => 'xml',
    ];
    
    protected function xml($app)
    {
        $app->response->setContentType('application/xml', 'utf-8');
        $xml = new \SimpleXMLElement('<xml/>');
        $array = $app->response->getContent();
        array_walk_recursive($array, [$xml, 'addChild']);
        $app->response->setContent($xml->asXML());        
    }
}
