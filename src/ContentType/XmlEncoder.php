<?php namespace Ovide\Libs\Mvc\Rest\ContentType;

use Ovide\Libs\Mvc\Rest;
use Ovide\Libs\Mvc\Rest\Exception\InternalServerError;

class XmlEncoder implements Rest\ContentType\Encoder
{
    
    const CONTENT_TYPE = 'application/xml';

    public function encode($data)
    {
        try {
            $xml = new \SimpleXMLElement('<xml/>');
            array_walk_recursive($data, [$xml, 'addChild']);

            $result = $xml->asXML();
        } catch (\Exception $ex) {
            $msg = "Couldn't generate an XML response";
            throw new InternalServerError($msg, Rest\Response::INTERNAL_ERROR, $ex);
        }
        
        return $result;
    }
}
