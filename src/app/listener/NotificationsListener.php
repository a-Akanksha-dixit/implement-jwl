<?php

namespace App\Listeners;

use Phalcon\Events\Event;
use Settings;
use Security;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Application;
use Phalcon\Http\Response;

class NotificationsListener extends Injectable
{
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
    public function defaultZipCode(
        Event $event,
        $component,
        $zip
    ) {
        if (empty($zip)) {
            $Settings = Settings::findFirst(['columns' => 'zipcode']);
            return $Settings->zipcode;
        } else {
            return $zip;
        }
    }
    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    {
        $aclFile = APP_PATH . '/security/acl.cache';
        if (true === is_file($aclFile)) {
            $acl = unserialize(file_get_contents($aclFile));
            $role = $application->request->get("role");
            $route = $this->router->getControllerName();
            $action = $this->router->getActionName();
            if (!$role || true !== $acl->isAllowed($role, $route, $action)) {
                echo "acces denied";
                die();
            }
        } else {
            echo "file does not exits";
            die();
        }
    }
}
