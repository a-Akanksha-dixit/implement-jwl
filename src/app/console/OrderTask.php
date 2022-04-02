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
        $now = $this->di->get('dateTime')->getTimeStamp();
        $orders = Orders::find("date = ".$now."");
        echo $orders;
    }
}