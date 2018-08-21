<?php
use \AcceptanceTester;
use Mocks\Controllers;
use Ovide\Phest;

class HeadersCest
{

    public function _before(AcceptanceTester $I)
    {
        Phest\App::addResources([
            'basic/foo' => Controllers\Foo::class
        ]);
    }

    public function _after(AcceptanceTester $I)
    {

    }

    public function testEtag(AcceptanceTester $I)
    {
        $uris = ['/basic/foo/1', '/basic/foo/2'];
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