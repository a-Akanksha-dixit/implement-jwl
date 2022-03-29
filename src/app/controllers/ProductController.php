<?php

use Phalcon\Mvc\Controller;


class ProductController extends Controller
{
    public function indexAction()
    {
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
                $product->save();
                if ($product) {
                    $this->response->setContent("Product Added successFully");
                    return;
                } else {
                    $this->response->setContent($product->getMessage());
                }
            } catch (Exception $e) {
                $this->response->setContent($e);
            }
        }
    }
}
