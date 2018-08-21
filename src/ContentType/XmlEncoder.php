<?php namespace Ovide\Phest\ContentType;

use Ovide\Phest\Exception\InternalServerError;

/**
 * 
 * @author Albert Ovide <albert@ovide.net>
 */
class XmlEncoder implements Encoder
{
    
    const CONTENT_TYPE = 'application/xml';

    public function encode($data, $root = 'root')
    {
        try {
            $result = self::toXml($data);
        } catch (\Exception $ex) {
            throw new InternalServerError('Couldn\'t generate a XML response');
        }
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
}
