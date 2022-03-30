<?php

use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;

class SecurityController extends Controller
{
    public function index()
    {
    }

    public function buildAclAction()
    {
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
