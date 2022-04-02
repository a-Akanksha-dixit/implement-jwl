<?php

// declare(strict_types=1);

namespace MyApp\Console;

use Phalcon\Cli\Task;
use Products;

class ProductTask extends Task
{
    // get count of products whose stock value is less than 10
    public function mainAction()
    {
        $produtcs = Products::find("stock < 10");
        echo 'Count of products whose stock value is less than 10 = '.count($produtcs);
    }
}
