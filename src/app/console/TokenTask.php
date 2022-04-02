<?php

// declare(strict_types=1);

namespace MyApp\Console;

use Phalcon\Cli\Task;
use Firebase\JWT\JWT;

class TokenTask extends Task
{
    public function createTokenAction($role = 'admin')
    {
        $key = "example_key";
        $now = $this->di->get('dateTime');
        // $timestap = $now->getTimestamp();
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => $now->getTimeStamp(),
            "nbf" => $now->modify("-1 minute")->getTimeStamp(),
            "exp" => $now->modify("+1 day")->getTimeStamp(),
            "sub" => $role
        );
        echo JWT::encode($payload, $key, 'HS256');
    }
    
}