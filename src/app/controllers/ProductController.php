<?php

use Phalcon\Mvc\Controller;
use App\models\Products as Products;


class ProductController extends Controller
{
    public function indexAction()
    {
        $this->view->products = Products::find();
    }
    public function addAction()
    {
        if ($_POST) {
            $escaper = new \App\components\MyEscaper();
            $name = $escaper->sanitize($this->request->getPost('product_name'));
            $description = $escaper->sanitize($this->request->getPost('product_description'));
            $tags = $escaper->sanitize($this->request->getPost('tags'));
            $price = $escaper->sanitize($this->request->getPost('price'));
            $stock = $escaper->sanitize($this->request->getPost('stock'));
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
