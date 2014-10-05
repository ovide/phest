<?php namespace Ovide\Libs\Mvc\Rest\Header;

abstract class Handler implements HandlerInterface {
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
    }
    
    public function init()
    {
        if (!static::HEADER) {
            $msg = get_class() . " constant 'HEADER' is not defined";
            throw new \LogicException($msg);
        }
        $this->_content = $this->_request->getHeader(static::HEADER);
    }
    
    public function get()
    {
        return $this->_content;
    }
}
