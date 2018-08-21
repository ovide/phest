<?php namespace Ovide\Phest\ContentType;

use Ovide\Phest\Exception\BadRequest;
use Ovide\Phest\Exception\InternalServerError;
use Codeception\TestCase\Test;

class JsonTest extends Test
{
    /**
     * @var \Ovide\Phest\UnitTester
     */
    protected $tester;
    /**
     * @var Json
     */
    protected $json;

    protected function _before()
    {
        $this->json = new Json();
    }
    
    public function testEncode()
    {
        $I = $this->tester;
        $data = ['foo' => 'bar'];
        $expected = '{"foo":"bar"}';
        
        $I->assertEquals($expected, $this->json->encode($data));
        
        try {
            $I->assertEquals($expected, $this->json->encode('foo'));
            $I->assertTrue(false, InternalServerError::class.' expected');
        } catch (InternalServerError $ex) {
            $I->assertTrue(true);
        }
    }
    
    public function testDecode()
    {
        $I = $this->tester;
        try {
            $this->json->decode('{foo');
            $I->assertFalse(true, BadRequest::class.' expected');
        } catch (BadRequest $ex) {
            $I->assertTrue(true);
        }
        
        $I->assertEquals(['foo' => 'bar'], $this->json->decode('{"foo":"bar"}'));
    }
}