<?php
namespace Rest\Header;


class AcceptLanguageTest extends \Codeception\TestCase\Test
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

    public function testGetAvailableLanguages()
    {
        $I = $this->tester;
        $rapp = new \ReflectionClass(App::class);
        $ral = $rapp->getProperty('availableLanguages');
        $ral->setAccessible(true);
        $ral->setValue(App::instance(), ['es', 'en']);
        $ral->setAccessible(false);
        
        $langs = App::getAvailableLanguages();
        $I->assertEquals(['es', 'en'], $langs);
    }
    
    /**
     * @depends testGetAvailableLanguages
     */
    public function testAddLanguages()
    {
        $I = $this->tester;
        
        App::addLanguages(['en','es']);
        $I->assertEquals(['en', 'es'], App::getAvailableLanguages());
        App::addLanguages(['en', 'ca']);
        $I->assertEquals(['en', 'es', 'ca'], App::getAvailableLanguages());
    }

}