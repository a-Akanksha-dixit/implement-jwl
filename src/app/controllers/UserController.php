<?php

use Phalcon\Mvc\Controller;

class UserController extends Controller
{
    public function addAction()
    {
        $this->view->roles = Roles::find();
        if ($_POST) {
            $escaper = new \App\Components\MyEscaper();
            $name = $escaper->sanitize($this->request->getPost('name'));
            $email = $escaper->sanitize($this->request->getPost('email'));
            $role = $escaper->sanitize($this->request->getPost('role'));
            $user = new Users();
            try {
                $user->assign(
                    array(
                        'name' => $name,
                        'email' => $email,
                        'role_id' => $role,
                    ),
                    [
                        'name',
                        'email',
                        'role_id'
                    ]
                );
                if ($user->save()) {
                    $this->flash->success('user Added successFully .Email Token : ' .
                        $this->di->get('EventsManager')->fire('notifications:generateToken', $this, $role));
                    return;
                } else {
                    $this->flash->error("One or More field is empty.");
                }
            } catch (Exception $e) {
                $this->flash->error($e);
            }
        }
    }
}
