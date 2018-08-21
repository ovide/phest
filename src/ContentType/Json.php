<?php namespace Ovide\Phest\ContentType;

use Ovide\Phest\Exception\BadRequest;
use Ovide\Phest\Exception\InternalServerError;

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
            throw new BadRequest('Invalid JSON data');
        }
        
        return $result;
    }

    public function encode($data)
    {
        if (!is_array($data)) {
            throw new InternalServerError("Couldn't generate a JSON response: data is not an array");
        }
        
        return $this->safeJsonEncode($data);
    }
    
    /**
     * @param array $value
     * @param int $options
     * @param int $depth
     * @return string
     * @throws InternalServerError
     * @link https://stackoverflow.com/questions/10199017/how-to-solve-json-error-utf8-error-in-php-json-decode
     */
    private function safeJsonEncode($value, $options = 0, $depth = 512)
    {
        $encoded = json_encode($value, $options, $depth);
        switch (json_last_error()) {
            case JSON_ERROR_NONE: return $encoded;
            case JSON_ERROR_UTF8: return $this->safeJsonEncode($this->utf8ize($value), $options, $depth);
            default:
                throw new InternalServerError("Couldn't generate a JSON response".PHP_EOL. json_last_error_msg());
        }
    }
    
    /**
     * @param mixed $mixed
     * @return mixed
     * @link http://php.net/manual/en/function.json-last-error.php#115980
     */
    private function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } else if (is_string($mixed)) {
            return utf8_encode($mixed);
        }
        
        return $mixed;
    }
}

