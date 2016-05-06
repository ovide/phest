<?php namespace Ovide\Libs\Mvc\Rest;

use Ovide\Libs\Mvc\FunctionalTester;

class HeadersCest
{
    /**
     * @var App
     */
    protected $app;

    public function _before(FunctionalTester $I)
    {
        $this->app = App::instance();
    }

    public function _after(FunctionalTester $I)
    {
        App::reset();
    }

    // tests
    public function testAccept(FunctionalTester $I)
    {
        $this->app->mountResource(\Mocks\Controllers\FooVar::class);
        
        $handlers = App::instance()->getHandlers();
        $accept = $handlers[HeaderHandler\Accept::HEADER];
        $accept->setAcceptable('application/xml', ContentType\XmlEncoder::class);

        $I->haveHttpHeader('Accept', 'application/xml');
        $I->sendGET('/foo/1/var');
        $expected = <<< EOXML
<?xml version="1.0"?>
<root><id>1</id><name>foo1</name><description>foo1 desc</description><id>2</id><name>foo2</name><description>foo2 desc</description></root>
EOXML;
        $I->seeResponseContains($expected);

        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('/foo/1/var');
        $I->seeResponseContainsJson();

        $I->haveHttpHeader('Accept', 'foo/bar');
        $I->sendGET('/foo/1/var');
    }
}
