<?php

use Ovide\Libs\Mvc\Rest\Header\AcceptLanguage;

class AcceptableLanguageTest extends \Codeception\TestCase\Test
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

    public function testParseAcceptableLanguages()
    {
        $I = $this->tester;
        
        $request  = new \Phalcon\Http\Request();
        
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ca,es;q=0.7,en;q=0.3';
        
        $header   = new AcceptLanguage($request);
        $expected = [
            'ca' => 1,
            'es' => 0.7,
            'en' => 0.3
        ];
        $I->assertEquals($expected ,$header->getAcceptableLanguageList());
    }

}