<?php

use Phalcon\Mvc\Controller;


class SettingController extends Controller
{
    public function indexAction()
    {
        $this->view->setting = Settings::findFirst();
        if ($_POST) {
            $escaper = new \App\Components\MyEscaper();
            $op = $escaper->sanitize($this->request->getPost('title'));
            $price = $escaper->sanitize($this->request->getPost('price'));
            $stock = $escaper->sanitize($this->request->getPost('stock'));
            $zip = $escaper->sanitize($this->request->getPost('zipcode'));
            $setting = Settings::findFirst();
            try {
                $setting->assign(
                    array(
                        'title_optimization' => $op,
                        'price' => $price,
                        'zipcode' => $zip,
                        'stock' => $stock,
                    ),
                    [
                        'title_optimization',
                        'price',
                        'zipcode',
                        'stock',
                    ]
                );
                if ($setting->save()) {
                    $this->flash->success("Settings updated successFully");
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
