<?php namespace Ovide\Libs\Mvc\Rest\Header;


class AcceptLanguage extends RequestHeader
{
    protected $_request;
    protected $_content;
    
    protected $_acceptable;
    
    const HEADER = 'HTTP_ACCEPT_LANGUAGE';
    
    protected function _init()
    {
        parent::_init();
        $this->_acceptable = $this->__parseAcceptableLanguages();
    }
    
    public function getAcceptableLanguageList()
    {
        return $this->_acceptable;
    }
    
    public function getBestLanguage($available)
    {
        $locale = null;
        while (($lang = key($this->_acceptable)) && (!$locale)) {
            next($this->_acceptable);
            $locale = \Locale::lookup($available, $lang, true);
        }
        return $locale;
    }
    
    /**
     * Parse the Accept-Language header
     * 
     * ca,es;q=0.7,en;q=0.3
     * 
     * returns an array ordered by 'q'
     * 
     * ca => 1
     * es => 0.7
     * en => 0.3
     * 
     * @param string $acceptable
     * @return array string => float
     */
    private final function __parseAcceptableLanguages()
    {
        $lang_parse = [];
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',$this->_content, $lang_parse);
        if (count($lang_parse[1])) {
            $langs = array_combine($lang_parse[1], $lang_parse[4]);
            foreach ($langs as $lang => $val) {
                if ($val === '') {
                    $langs[$lang] = 1;
                }
            }
            arsort($langs, SORT_NUMERIC);
            return $langs;
        }
        return [];
    }
}
