<?php
use \AcceptanceTester;
use Ovide\Libs\Mvc\Rest;

class LanguageCest
{
    /**
     * @var Rest\App
     */
    protected $app;
    
    public function _before(AcceptanceTester $I)
    {
        $app = Rest\App::instance();
        $app->addResource('lang', LangMock::class);
        $app->addLanguages(['en', 'es']);
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testNothingSentResolvesDefault(AcceptanceTester $I)
    {
        $I->sendGET('/lang');
        $I->seeResponseEquals(json_encode(['en']));
    }
    
    public function testAcceptOneLanguageButNoMatch(AcceptanceTester $I)
    {
        $I->haveHttpHeader('Accept-Language', 'ru');
        $I->sendGET('/lang');
        $I->seeResponseEquals(json_encode(['en']));
    }
    
    public function testAcceptAndMatchOneLanguage(AcceptanceTester $I)
    {
        $I->haveHttpHeader('Accept-Language', 'ru');
        $I->sendGET('/lang');
        $I->seeResponseEquals(json_encode(['en']));
    }
    
    public function testAcceptMultipeLanguagesButNoMatch(AcceptanceTester $I)
    {
        $I->haveHttpHeader('Accept-Language', 'ru,de,nl');
        $I->sendGET('/lang');
        $I->seeResponseEquals(json_encode(['en']));
    }
    
    public function testAcceptMultipleLanguagesAndMatchFirst(AcceptanceTester $I)
    {
        $I->haveHttpHeader('Accept-Language', 'es,de,nl');
        $I->sendGET('/lang');
        $I->seeResponseEquals(json_encode(['es']));
    }
    
    public function testAcceptMultipleLanguagesAndMatchAny(AcceptanceTester $I)
    {
        $I->haveHttpHeader('Accept-Language', 'de,es,nl');
        $I->sendGET('/lang');
        $I->seeResponseEquals(json_encode(['es']));
    }
    
    public function testAcceptMultipleLanguagesWithQButNoMatch(AcceptanceTester $I)
    {
        $I->haveHttpHeader('Accept-Language', 'ru,de;q=0.4,nl;q=0.3');
        $I->sendGET('/lang');
        $I->seeResponseEquals(json_encode(['en']));
    }
    
    public function testAcceptMulipleLanguagesWithQAndMatchAny(AcceptanceTester $I)
    {
        $I->haveHttpHeader('Accept-Language', 'ru,en;q=0.2,es;q=0.4');
        $I->sendGET('/lang');
        $I->seeResponseEquals(json_encode(['es']));
    }
    
    public function testAcceptMultipleLanguagesAndControllerCanMatch(AcceptanceTester $I)
    {
        $I->haveHttpHeader('Accept-Language', 'ru,ca;q=0.5,es;q=0.4');
        $I->sendGET('/lang');
        $I->seeResponseEquals(json_encode(['ca']));
    }
    
    public function testAcceptMultipleLanguagesAndControllerDisallowMatch(AcceptanceTester $I)
    {
        $I->haveHttpHeader('Accept-Language', 'ru,ca;q=0.5,es;q=0.4');
        $I->sendGET('/lang/es');
        $I->seeResponseEquals(json_encode(['es']));
    }
}
