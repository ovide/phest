<?php

use Ovide\Libs\Mvc\Rest;

class LangMock extends Rest\Controller
{
    public static $data = [
        'en' => 'This is the default text',
        'es' => 'Este texto es en español',
        'ca' => 'En català millor'
    ];
    
    protected $availableLanguages = ['es', 'ca'];
    
    public function get()
    {
        return [self::$data[$this->locale]];
    }
    
    public function getOne()
    {
        $this->disallowLanguage('ca');
        $this->getBestLang();
        return $this->get();
    }
}
