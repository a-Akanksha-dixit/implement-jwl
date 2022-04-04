<?php

use Phalcon\Mvc\Controller;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

class IndexController extends Controller
{
    public function indexAction()
    {
        /**
         * get welcome heading
         */
        $locale = new App\Components\Locale();
        $this->view->head = $locale->getTranslation('welcome');
    }
}
