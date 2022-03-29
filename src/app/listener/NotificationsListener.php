<?php

namespace App\Listeners;

use Phalcon\Events\Event;
use Phalcon\Logger;
use App\Components\NotificationsAware;
use Settings;

class NotificationsListener
{
    public function optimize(
        Event $event,
        NotificationsAware $component,
        $data
    ) {
        $Settings = Settings::findFirst(['columns' => 'title_optimization']);
        if ($Settings->title_optimization == 'on') {
            return $data['title'] . ' ' . $data['tags'];
        } else {
            return $data['title'];
        }
    }
    public function price(
        Event $event,
        NotificationsAware $component,
        $price
    ) {
        if (empty($price)) {
            $Settings = Settings::findFirst(['columns' => 'price']);
            return $Settings->price;
        } else {
            return $price;
        }
    }
    public function stock(
        Event $event,
        NotificationsAware $component,
        $stock
    ) {
        if (empty($stock)) {
            $Settings = Settings::findFirst(['columns' => 'stock']);
            return $Settings->stock;
        } else {
            return $stock;
        }
    }
    public function zipcode(
        Event $event,
        NotificationsAware $component,
        $zip
    ) {
        if (empty($zip)) {
            $Settings = Settings::findFirst(['columns' => 'zipcode']);
            return $Settings->zipcode;
        } else {
            return $zip;
        }
    }
}
