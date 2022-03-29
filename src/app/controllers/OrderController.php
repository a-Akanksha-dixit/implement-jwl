<?php

use Phalcon\Mvc\Controller;

class OrderController extends Controller
{
    public function indexAction()
    {
        $query = $this
            ->modelsManager
            ->createQuery(
                'SELECT Products.name as product_name, Orders.* FROM Orders join Products on Orders.product_id=Products.product_id'
            );

            $this->view->orders =$query->execute();
    }
    public function addAction()
    {
    }
}
