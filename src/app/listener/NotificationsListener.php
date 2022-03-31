<?php

namespace App\Listeners;

use Phalcon\Events\Event;
use Settings;
use Roles;
use Components;
use Permissions;
use Phalcon\Di\Injectable;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Exception;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

/**
 * event listener class
 */
class NotificationsListener extends Injectable
{

    /**
     * title optimization  event
     *
     * @param Event $event
     * @param [type] $component
     * @param [type] $data
     * @return void
     */
    public function titleOptimization(
        Event $event,
        $component,
        $data
    ) {
        $Settings = Settings::findFirst(['columns' => 'title_optimization']);
        if ($Settings->title_optimization == 'on') {
            return $data['title'] . ' ' . $data['tags'];
        } else {
            return $data['title'];
        }
    }

    /**
     * set default price event
     *
     * @param Event $event
     * @param [type] $component
     * @param [type] $price
     * @return void
     */
    public function defaultPrice(
        Event $event,
        $component,
        $price
    ) {

        if (empty($price)) {
            $Settings = Settings::findFirst(['columns' => 'price']);
            return $Settings->price;
        } else {
            return $price;
        }
    }

    /**
     * set default stock event
     *
     * @param Event $event
     * @param [type] $component
     * @param [type] $stock
     * @return void
     */
    public function defaultStock(
        Event $event,
        $component,
        $stock
    ) {
        if (empty($stock)) {
            $Settings = Settings::findFirst(['columns' => 'stock']);
            return $Settings->stock;
        } else {
            return $stock;
        }
    }

    /**
     * set default zip code event
     *
     * @param Event $event
     * @param [type] $component
     * @param [type] $zip
     * @return void
     */
    public function defaultZipCode(
        Event $event,
        $component,
        $zip
    ) {
        // return 'test';
        if (empty($zip)) {
            $Settings = Settings::findFirst(['columns' => 'zipcode']);
            return $Settings->zipcode;
        } else {
            return $zip;
        }
    }

    /**
     * before loading url event
     *
     * @param Event $event
     * @param \Phalcon\Mvc\Application $application
     * @return void
     */
    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    {
        $bearer = $application->request->get("bearer");
        if ($bearer) {
            $parser = new Parser();
            $now = $this->di->get('dateTime');
            $expires = $now->getTimestamp();
            $token = $parser->parse($bearer);
            try {
                $validator = new Validator($token, 100);
                $validator->validateExpiration($expires);
                $role_id = $token->getClaims()->getPayload()['sub'];
                $role = Roles::findFirst($role_id)->role;
                // die($role);
                $aclFile = APP_PATH . '/security/acl.cache';
                if (true === is_file($aclFile)) {
                    $acl = unserialize(file_get_contents($aclFile));
                    $route = $this->router->getControllerName() ?? 'index';
                    $action = $this->router->getActionName() ?? 'index';
                    if (true !== $acl->isAllowed($role, $route, $action)) {
                        echo "<h1>You do not have access to this page</h1>";
                        die();
                    }
                } else {
                    $this->di->get('EventsManager')->fire('notifications:getPermissions', $this);
                }
            } catch (Exception $e) {
                echo '<h1>Token validation failed.</h1>';
                die;
            }
        } else {
            echo '<h1>Bearer is not found</h1>';
            die;
        }
    }

    /**
     * get user permissions from db event
     *
     * @param Event $event
     * @param [type] $component
     * @return void
     */
    public function getPermissions(
        Event $event,
        $component
    ) {
        $aclFile = APP_PATH . '/security/acl.cache';
        if (true !== is_file($aclFile)) {
            $acl = new Memory();
        } else {
            $acl = unserialize(
                file_get_contents($aclFile)
            );
        }
        $roles = Roles::find();
        $components = Components::find();
        $permissions = Permissions::find();
        foreach ($roles as $r) {
            $acl->addRole($r->role);
        }
        foreach ($components as $com) {
            $action = explode(',', $com->actions);
            $acl->addComponent(
                $com->name,
                $action
            );
        }
        foreach ($permissions as $per) {
            $acl->allow($per->role, $per->component, $per->action);
            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        }
        file_put_contents(
            $aclFile,
            serialize($acl)
        );
    }


    public function generateToken(
        Event $event,
        $component,
        $role
    ) {
        $signer  = new Hmac();
        $builder = new Builder($signer);
        $now = $this->di->get("dateTime");
        // $now        = new DateTimeImmutable();
        $issued     = $now->getTimestamp();
        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk';

        // Setup
        $builder
            // ->setAudience('https://target.phalcon.io')  // aud
            ->setContentType('application/json')        // cty - header
            ->setExpirationTime($expires)               // exp 
            // ->setId('abc')                    // JTI id 
            ->setIssuedAt($issued)                      // iat 
            ->setIssuer('https://phalcon.io')           // iss 
            ->setNotBefore($notBefore)                  // nbf
            ->setSubject($role)   // sub
            ->setPassphrase($passphrase);
        // password 
        $tokenObject = $builder->getToken();

        // The token
        return $tokenObject->getToken();
    }
}
