<?php namespace Ovide\Libs\Mvc\Rest\HeaderHandler;


class AcceptTest extends \Codeception\TestCase\Test
{
    /**
     * @var \Ovide\Libs\Mvc\UnitTester
     */
    protected $tester;

    protected function _before()
    {
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
}