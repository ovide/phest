<?php namespace Ovide\Libs\Mvc\Rest\Header;

abstract class RequestHeader {
    /**
     * @var \Phalcon\Http\RequestInterface
     */
    protected $_request;
    /**
     * @var string
     */
    protected $_content;
    
    const HEADER = '';
    
    public function __construct(\Phalcon\Http\RequestInterface $r)
    {
        $this->_request = $r;
        $this->_init();
    }
    
    protected function _init()
    {
        if (!static::HEADER) {
            throw new \LogicException(get_class() . " constant 'HEADER' is not defined");
        }
        $this->_content = $this->_request->getHeader(static::HEADER);
    }
    
    public function get()
    {
        return $this->_content;
    }
}
