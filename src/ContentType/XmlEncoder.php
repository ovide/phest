<?php namespace Ovide\Libs\Mvc\Rest\ContentType;

use Ovide\Libs\Mvc\Rest;
use Ovide\Libs\Mvc\Rest\Exception\InternalServerError;

class XmlEncoder implements Rest\ContentType\Encoder
{
    
    const CONTENT_TYPE = 'application/xml';

    public function encode($data, $root = 'root')
    {
        $result = self::toXml($data);
        return '<?xml version="1.0"?>'."\n<root>$result</root>";
    }
    
    private static function toXml($array)
    {
        $result = '';
        foreach ($array as $key => $value) {
            $result .= (is_array($value)) ? self::toXml($value) : "<$key>".htmlentities($value)."</$key>";
        }
        
        return $result;
    }
    
    private static function addChild(\SimpleXMLElement $element, $key, $value)
    {
        if ( !is_array( $value ) ) {
            $element->addChild( $key, $value );
        } else {
            $nested = $element->addChild( $key );
            foreach ( $value as $key2 => $value2 ) {
                self::addChild( $nested, $key2, $value2 );
            }
        }
    }
}
