<?php namespace Ovide\Phest\ContentType;

use Ovide\Phest\Exception\BadRequest;
use Ovide\Phest\Exception\InternalServerError;
use Codeception\TestCase\Test;

class XmlTest extends Test
{
    /**
     * @var \Ovide\Phest\UnitTester
     */
    protected $tester;
    /**
     * @var Json
     */
    protected $encoder;

    protected function _before()
    {
        $this->encoder = new XmlEncoder();
    }
    
    public function testEncode()
    {
        $I = $this->tester;
        $data = ['foo' => 'bar'];
        $expected = '<?xml version="1.0"?>'."\n<root><foo>bar</foo></root>";
        
        $I->assertEquals($expected, $this->encoder->encode($data));
        
        try {
            $I->assertEquals($expected, $this->encoder->encode('foo'));
            $I->assertTrue(false, InternalServerError::class.' expected');
        } catch (InternalServerError $ex) {
            $I->assertTrue(true);
        }
    }
}