<?php

/*if (!class_exists('FunctionalApp')) {
    class FunctionalApp extends \Ovide\Libs\Mvc\Rest\App
    {
        public function handle($uri=NULL)
        {
            return parent::handle($_GET['url']);
        }
    }
}

return FunctionalApp::instance();*/
return Ovide\Libs\Mvc\Rest\App::instance();
