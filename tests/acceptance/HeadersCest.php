<?php
use \AcceptanceTester;
use Ovide\Libs\Mvc\Rest;

require_once __DIR__.'/../mocks/BasicMock.php';
require_once __DIR__.'/../mocks/FooMock.php';

class HeadersCest
{
    
    public function _before(AcceptanceTester $I)
    {
        Rest\App::addResources([
            'basic'     => BasicMock::class,
            'basic/foo' => FooMock::class
        ]);
    }

    public function _after(AcceptanceTester $I)
    {
        
    }

    public function testEtag(AcceptanceTester $I)
    {
        $uris = ['/basic/1', '/basic/2'];
        foreach ($uris as $uri) {
            $I->sendGET($uri);
            $I->seeHttpHeader('ETag');
            $etag = $I->grabHttpHeader('ETag');

            $I->sendGET('/basic/34');
            $I->dontSeeHttpHeader('ETag');

            $I->sendGET($uri);
            $I->seeHttpHeader('ETag', $etag);

            $I->haveHttpHeader('If-None-Match', $etag);
            $I->sendGET($uri);
            $I->seeResponseCodeIs(304);
        }
    }
}