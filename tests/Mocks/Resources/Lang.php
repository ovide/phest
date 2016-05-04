<?php namespace Mocks\Controllers;

use Igm\Rest;

class Lang extends Rest\Controller
{

    protected $_availableLanguages = ['es', 'ca'];

    public function get()
    {
        return [$this->_locale];
    }

    public function getOne()
    {
        $this->disallowLanguage('ca');
        $this->getBestLang();
        return $this->get();
    }
}
