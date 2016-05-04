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
        $this->app->addHeaderHandler(new \Mocks\Middlewares\Accept());

        $I->haveHttpHeader('Accept', 'application/xml');
        $I->sendGET('/foo/1/var');
        $expected = <<< EOXML
<?xml version="1.0"?>
<xml><1>id</1><foo1>name</foo1><foo1 desc>description</foo1 desc><2>id</2><foo2>name</foo2><foo2 desc>description</foo2 desc></xml>
EOXML;
        $I->seeResponseContains($expected);

        $I->haveHttpHeader('Accept', 'application/json');
        $I->sendGET('/foo/1/var');
        $I->seeResponseContainsJson();

        $I->haveHttpHeader('Accept', 'foo/bar');
        $I->sendGET('/foo/1/var');
    }
}