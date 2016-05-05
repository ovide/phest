<?php namespace Ovide\Libs\Mvc\Rest;

class Response extends \Phalcon\Http\Response
{
    const OK              = 200;
    const CREATED         = 201;
    const ACCEPTED        = 202;
    const NO_CONTENT      = 204;

    const MOVED           = 301;
    const NOT_MODIFIED    = 304;

    const BAD_REQUEST     = 400;
    const UNAUTHORIZED    = 401;
    const FORBIDDEN       = 403;
    const NOT_FOUND       = 404;
    const NOT_ALLOWED     = 405;
    const NOT_ACCEPTABLE  = 406;
    const CONFLICT        = 409;
    const GONE            = 410;
    const UNSUPPORTED_MT  = 415;

    const INTERNAL_ERROR  = 500;
    const NOT_IMPLEMENTED = 501;
    const UNAVAILABLE     = 502;

    public static $status = array(
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',

        301 => 'Moved Permanently',
        304 => 'Not Modified',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method not allowed',
        406 => 'Not Acceptable',
        409 => 'Conflict',
        410 => 'Gone',
        415 => 'Unsupported media type',

        500 => 'Internal server error',
        501 => 'Not implemented',
        503 => 'Service unavailable',
    );

    public function rebuild($content = null, $code = null, $status = null)
    {
        $this->_content = $content;
        $this->_cookies = null;
        $this->_file    = null;
        //$this->_headers = new \Phalcon\Http\Response\Headers();
        $this->_sent    = 0;
        if ($this->_content) {
            if (!$code) {
                $code = self::OK;
            }
        } elseif (!$code) {
            $code = self::NO_CONTENT;
        }
        if (!$status) {
            $status = isset(self::$status[$code]) ? self::$status[$code] : '';
        }
        
        $this->setStatusCode($code, $status);
    }

    /**
     * Build a Not Found standard response
     *
     * @param \Phalcon\HTTP\ResponseInterface $rsp
     */
    public function notFound()
    {
        $this->_sent = 0; //TODO Fix it!
        $this->setStatusCode(self::NOT_FOUND, self::$status[self::NOT_FOUND]);
        $this->_content = '';
    }
    
    public function encodeContent()
    {
        /* @var $encoder Ovide\Libs\Mvc\Rest\ContentType\Encoder */
        $encoder = $this->_dependencyInjector->get('responseWriter');
        $this->_content = $encoder->encode($this->_content);
        if ($encoder::CONTENT_TYPE) {
            $this->setContentType($encoder::CONTENT_TYPE);
        }
    }
}
