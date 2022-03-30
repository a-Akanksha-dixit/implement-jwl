<?php

use Phalcon\Mvc\Controller;

class ProductController extends Controller
{
    public function indexAction()
    {
        $this->view->products = Products::find();
    }
    public function addAction()
    {
        if ($_POST) {
            $escaper = new \App\Components\MyEscaper();
            $name = $escaper->sanitize($this->request->getPost('product_name'));
            $description = $escaper->sanitize($this->request->getPost('product_description'));
            $tags = $escaper->sanitize($this->request->getPost('tags'));
            $price = $escaper->sanitize($this->request->getPost('price'));
            $stock = $escaper->sanitize($this->request->getPost('stock'));
            $name = $this->di->get('EventsManager')->fire(
                'notifications:titleOptimization',
                $this,
                array('title' => $name, 'tags' => $tags)
            );
            $price = $this->di->get('EventsManager')->fire('notifications:defaultPrice', $this, $price);
            $stock = $this->di->get('EventsManager')->fire('notifications:defaultStock', $this, $stock);
            $product = new Products();
            try {
                $product->assign(
                    array(
                        'name' => $name,
                        'description' => $description,
                        'tags' => $tags,
                        'price' => $price,
                        'stock' => $stock
                    ),
                    [
                        'name',
                        'description',
                        'tags',
                        'price',
                        'stock'
                    ]
                );
                if ($product->save()) {
                    $this->flash->success("Product Added successFully");
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
