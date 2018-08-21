<?php namespace Ovide\Phest\HeaderHandler;


class AcceptTest extends \Codeception\TestCase\Test
{
    /**
     * @var \Ovide\Phest\UnitTester
     */
    protected $tester;

    protected function _before()
    {
        \Ovide\Phest\App::reset();
    }

    protected function _after()
    {
    }

    public function testMatch()
    {
        $I = $this->tester;
        $obj = new Accept();
        $json = 'application/json';
        $xml  = 'application/xml';
        $html = 'text/html';
        $txt = 'text/plain';
        $options = [$json, $xml, $html, $txt];
        $ro = new \ReflectionObject($obj);
        $rm = $ro->getMethod('match');
        $rm->setAccessible(true);
        $I->assertEquals($json, $rm->invokeArgs($obj, ['application/json', $options]));
        $I->assertEquals($xml , $rm->invokeArgs($obj, ['application/xml' , $options]));
        $I->assertEquals($json, $rm->invokeArgs($obj, ['application/*'   , $options]));
        $I->assertEquals($html, $rm->invokeArgs($obj, ['text/*'          , $options]));
        $I->assertEquals($json, $rm->invokeArgs($obj, ['*/*'             , $options]));
        $I->assertEquals($txt , $rm->invokeArgs($obj, ['text/plain'      , $options]));
        $I->assertEquals(null , $rm->invokeArgs($obj, ['foo/*'           , $options]));
        $I->assertEquals(null , $rm->invokeArgs($obj, ['foo/bar'         , $options]));
        $I->assertEquals($json, $rm->invokeArgs($obj, [''                , $options]));
        $I->expectException(new \RuntimeException("Unmatchable media type: */bar"),
        function () use($rm, $obj, $options) {
            $rm->invokeArgs($obj, ['*/bar', $options]);
        });
        $I->expectException(new \RuntimeException("No media types available"), 
        function() use($rm, $obj) {
            $rm->invokeArgs($obj, ['*/*', []]);
        });
    }
    
    public function testBeforeExecuteRouteAndSetSupported()
    {
        $I = $this->tester;
        $app = \Ovide\Phest\App::instance();
        
        $obj = new Accept();
        $evt = new \Phalcon\Events\Event('foo', 'bar');
        $data = [];
        
        $obj->beforeExecuteRoute($evt, $app, $data);
        $I->assertInstanceOf(\Ovide\Phest\ContentType\Json::class, $app->di->get('requestReader'));
              
        $_SERVER['CONTENT_TYPE'] = 'foo/bar';        
        try {
            $obj->beforeExecuteRoute($evt , $app, $data);
            $I->assertTrue(false, \Ovide\Phest\Exception\UnsupportedMediaType::class.' expected');
        } catch (\Ovide\Phest\Exception\UnsupportedMediaType $ex) {
            $I->assertTrue(true);
            $app->response->rebuild();
        }
        
        $obj->setSupported(\Ovide\Phest\ContentType\Json::class, 'foo/bar');
        $obj->beforeExecuteRoute($evt, $app, $data);
        
        
        unset($_SERVER['CONTENT_TYPE']);
    }
    
    public function testBeforeExceptionSetAcceptable()
    {
        $I      = $this->tester;
        $app    = \Ovide\Phest\App::instance();
        $evt    = new \Phalcon\Events\Event('foo', 'bar');
        $data   = [];
        $_SERVER['HTTP_ACCEPT'] = 'text/csv';
        $obj    = new Accept();
        
        $obj->beforeException($evt, $app, $data);
        
        $I->assertInstanceOf(\Ovide\Phest\ContentType\Json::class, $app->di->get('responseWriter'));
        
        $obj->beforeException($evt, $app, $data);
        $I->assertInstanceOf(\Ovide\Phest\ContentType\Json::class, $app->di->get('responseWriter'));
        $obj->setAcceptable(\Ovide\Phest\ContentType\XmlEncoder::class, 'text/csv');
        $obj->beforeException($evt, $app, $data);
        $I->assertInstanceOf(\Ovide\Phest\ContentType\XmlEncoder::class, $app->di->get('responseWriter'));
        
        unset($_SERVER['HTTP_ACCEPT']);
    }
    
    public function testAfterException()
    {
        $I      = $this->tester;
        $app    = \Ovide\Phest\App::instance();
        $evt    = new \Phalcon\Events\Event('foo', 'bar');
        $obj    = new Accept();
        
        $obj->afterException($evt, $app, new \Ovide\Phest\Exception\UnsupportedMediaType());
        $I->assertEquals('application/json', $app->response->getHeaders()->get('Accept'));
    }
}