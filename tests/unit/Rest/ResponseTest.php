<?php
namespace Ovide\Libs\Mvc\Rest;


class ResponseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \Ovide\Libs\Mvc\UnitTester
     */
    protected $tester;

    /**
     *
     * @var Response
     */
    protected $response;

    protected function _before()
    {
        $this->response = App::instance()->response;
    }

    protected function _after()
    {

    }

    // tests
    public function testRebuild()
    {
        $expectedContent = ['foo' => 'bar'];
        $expectedHeaders = new \Phalcon\Http\Response\Headers();
        $expectedHeaders->set('Status', '200 OK');
        $expectedHeaders->set('HTTP/1.1 200 OK', null);

        $this->response->rebuild(['foo' => 'bar']);

        $this->assertEquals($expectedContent, $this->response->getContent());
        $this->assertEquals($expectedHeaders, $this->response->getHeaders());
    }

}