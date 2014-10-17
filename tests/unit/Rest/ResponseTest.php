<?php
namespace Rest;

use Ovide\Libs\Mvc\Rest\Response;

class ResponseTest extends \Codeception\TestCase\Test
{
    /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testConstructorNoContentNoCodeNoMessage()
    {
        $I = $this->tester;

        $r = new Response();
        $h = $r->getHeaders();

        $I->assertEquals('204 No Content', $h->get('Status'));
        $I->assertNull($r->getContent());
    }

    public function testConstructorNoContentNoCodeMessage()
    {
        $I = $this->tester;

        $msg = 'Hi there';
        $r = new Response(null, null, $msg);
        $h = $r->getHeaders();

        $I->assertEquals("204 $msg", $h->get('Status'));
        $I->assertNull($r->getContent());
    }

    public function testConstructorNoContentCodeNoMessage()
    {
        $I = $this->tester;

        $code = Response::NOT_IMPLEMENTED;

        $r = new Response(null, $code);
        $h = $r->getHeaders();

        $I->assertEquals("$code ".Response::$status[$code], $h->get('Status'));
        $I->assertNull($r->getContent());
    }

    public function testConstructorNoContentUndfinedCodeNoMessage()
    {
        $I = $this->tester;

        $code = 555;

        $r = new Response(null, $code);
        $h = $r->getHeaders();

        $I->assertEquals("$code ", $h->get('Status'));
        $I->assertNull($r->getContent());
    }

    public function testConstructorNoContentCodeMessage()
    {
        $I = $this->tester;

        $code = 555;
        $msg  = 'Hi there';

        $r = new Response(null, $code, $msg);
        $h = $r->getHeaders();

        $I->assertEquals("$code $msg", $h->get('Status'));
        $I->assertNull($r->getContent());
    }

    public function testConstructorContentNoCodeNoMessage()
    {
        $I = $this->tester;

        $content = ['foo'];
        $r = new Response($content);
        $h = $r->getHeaders();

        $I->assertEquals('200 OK', $h->get('Status'));
        $I->assertEquals('application/json', $h->get('Content-Type'));
        $I->assertEquals(json_encode($content), $r->getContent());
    }

    public function testConstructorContentNoCodeMessage()
    {
        $I = $this->tester;

        $content = ['foo'];
        $msg     = 'Hi there';

        $r = new Response($content, null, $msg);
        $h = $r->getHeaders();

        $I->assertEquals("200 $msg", $h->get('Status'));
        $I->assertEquals('application/json', $h->get('Content-Type'));
        $I->assertEquals(json_encode($content), $r->getContent());
    }

    public function testConstructorContentCodeNoMessage()
    {
        $I = $this->tester;

        $content = ['foo'];
        $code    = Response::CONFLICT;

        $r = new Response($content, $code);
        $h = $r->getHeaders();

        $I->assertEquals("$code ".Response::$status[$code], $h->get('Status'));
        $I->assertEquals('application/json', $h->get('Content-Type'));
        $I->assertEquals(json_encode($content), $r->getContent());
    }

    public function testConstructorContentCodeMessage()
    {
        $I = $this->tester;

        $content = ['foo'];
        $code    = 200;
        $msg     = 'Alright';

        $r = new Response($content, $code, $msg);
        $h = $r->getHeaders();

        $I->assertEquals("$code $msg", $h->get('Status'));
        $I->assertEquals('application/json', $h->get('Content-Type'));
        $I->assertEquals(json_encode($content), $r->getContent());
    }
}
