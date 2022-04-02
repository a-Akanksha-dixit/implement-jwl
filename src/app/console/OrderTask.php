<?php

// declare(strict_types=1);

namespace MyApp\Console;

use Phalcon\Cli\Task;
use Orders;

class OrderTask extends Task
{
    //task to fetch todays newly created order
    public function mainAction()
    {
        // $yesterday = date('d.m.Y', strtotime("-1 days"));
        // // $now = date("Y-m-d h:i:s");
        // echo $yesterday;
        // die;
        // echo $now;
        $orders = $this
            ->modelsManager
            ->createQuery(
                "select * from Orders where DATE(date) = DATE(NOW())"
            )->execute();
        echo json_encode($orders);
    }
}
