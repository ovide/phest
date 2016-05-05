<?php namespace Ovide\Libs\Mvc\Rest\ContentType;

/**
 * Description of Json
 *
 * @author Albert Ovide <albert@ovide.net>
 */
class Json implements Encoder, Decoder
{
    const CONTENT_TYPE = 'application/json';
    
    public function decode($data)
    {
        $result = json_decode($data, true);
        if ($result === null) {
            throw new \Ovide\Libs\Mvc\Rest\Exception\BadRequest('Invalid JSON data');
        }
        
        return $result;
    }

    public function encode($data)
    {
        if (!is_array($data) || (($result = json_encode($data)) === false)) {
            throw new \Ovide\Libs\Mvc\Rest\Exception\InternalServerError("Couldn't generate a JSON response");
        }
        
        return $result;
    }
}
