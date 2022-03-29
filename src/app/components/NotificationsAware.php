<?php

namespace App\Components;

use Phalcon\Di\Injectable;

/**
 * @property Logger $logger
 */
class NotificationsAware extends Injectable
{
    public function __construct()
    {
        $this->component = $this->di->get('EventsManager');
    }
    public function titleOptimization($product)
    {
        return $this->component->fire('notifications:optimize', $this, $product);
    }
    public function defaultPrice($price)
    {
        return $this->component->fire('notifications:price', $this, $price);
    }
    public function defaultStock($stock)
    {
        return $this->component->fire('notifications:stock', $this, $stock);
    }
    public function defaultZipCode($zip)
    {
        return $this->component->fire('notifications:zipcode', $this, $zip);
    }
}
