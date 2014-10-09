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

    public function __construct($content = null, $code = null, $status = null)
    {
        parent::__construct($content, $code, $status);
        if ($this->_content) {
            /*
            if (($code === null || $code < 300) && ($this->eTag())){
                $this->_content = '';
                return;
            }
             */
            $this->buildBody();
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
     * Check if response ETag matches with the request header If-None-Match
     * Clear the content body if matches and set the status code 304
     *
     * @param  string  $content
     * @return boolean true if matches
     */
    /*
    protected function eTag()
    {

        $et = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : '';
        //$et = filter_input(INPUT_SERVER, 'HTTP_IF_NONE_MATCH');
        $net = '"'.md5(serialize($this->_content)).'"';
        $this->setEtag($net);
        if ($et === $net) {
            $this->setStatusCode(self::NOT_MODIFIED,
                    self::$status[self::NOT_MODIFIED]);
            return true;
        }
        return false;
    }
     *
     */

    /**
     * Build the body using an acceptable format
     * @todo Must add ResponseFormatters (xml, csv, xls,...)
     *
     * @param \Phalcon\HTTP\ResponseInterface $rsp
     * @param array                           $content
     */
    protected function buildBody()
    {
        $this->setContentType('application/json');
        $this->setJsonContent($this->_content);
    }

    /**
     * Build a Not Found standard response
     *
     * @param \Phalcon\HTTP\ResponseInterface $rsp
     */
    public function notFound()
    {
        $this->setStatusCode(self::NOT_FOUND, self::$status[self::NOT_FOUND]);
        $this->_content = [
            'message' => self::$status[self::NOT_FOUND],
            'code'    => self::NOT_FOUND,
        ];
        $this->buildBody();
    }
}
