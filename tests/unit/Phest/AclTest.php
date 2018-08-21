<?php namespace Ovide\Phest;

use Codeception\TestCase\Test;

class AclTest extends Test
{
    /**
     * @var \Ovide\Phest\UnitTester
     */
    protected $tester;
    /**
     * @var Acl
     */
    protected $acl;

    protected function _before()
    {
        App::reset();
        $app = App::instance();
        $app->addResources([\Mocks\Controllers\Basic::class, \Mocks\Controllers\Foo::class]);
        $app->di->setShared('acl', Acl::class);
        $this->acl = $app->di->get('acl');
        $this->acl->setDefaultAction(\Phalcon\Acl::DENY);
    }

    protected function _after()
    {
    }

    // tests
    public function testReload()
    {
        $this->acl->reload();
        $this->assertTrue($this->acl->isAllowed('guest', '/', 'get'));
        $this->assertFalse($this->acl->isAllowed('registered', '/', 'post'));
    }
}