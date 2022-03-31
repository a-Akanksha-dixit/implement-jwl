<?php

namespace App\Listeners;

use Phalcon\Events\Event;
use Settings;
use Roles;
use Components;
use Permissions;
use Phalcon\Di\Injectable;
use Phalcon\Acl\Adapter\Memory;

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
        $aclFile = APP_PATH . '/security/acl.cache';
        if (true === is_file($aclFile)) {
            $acl = unserialize(file_get_contents($aclFile));
            $role = $application->request->get("role") ?? 'guest';
            $route = $this->router->getControllerName()?? 'index';
            $action = $this->router->getActionName() ?? 'index';
            // $role = empty($role)?'manager':$role;
            if (true !== $acl->isAllowed($role, $route, $action)) {
                echo "<h1>You do not have access to this page</h1>";
                die();
            }
        } else {
            $this->di->get('EventsManager')->fire('notifications:getPermissions', $this);
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
}
