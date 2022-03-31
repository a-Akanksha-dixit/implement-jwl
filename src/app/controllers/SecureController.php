<?php

use Phalcon\Mvc\Controller;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

class SecureController
{
    public function index()
    {
        echo 'this';
    }

    public function getTokenAction()
    {
        
        // echo "<br>".$tokenObject->subject();
    }
}
