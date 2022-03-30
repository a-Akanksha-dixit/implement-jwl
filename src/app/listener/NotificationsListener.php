<?php

namespace App\Listeners;

use Phalcon\Events\Event;
use Settings;

class NotificationsListener
{
    public function titleOptimization(
        Event $event,
        $component,
        $data
    ) {
        $Settings = Settings::findFirst(['columns' => 'title_optimization']);
        if ($Settings->title_optimization == 'on') {
            return $data['title'] . ' ' . $data['tags'];
        } else {
            return $data['title'];
        }
    }
    public function defaultPrice(
        Event $event,
        $component,
        $price
    ) {
        
        if (empty($price)) {
            $Settings = Settings::findFirst(['columns' => 'price']);
            return $Settings->price;
        } else {
            return $price;
        }
    }
    public function defaultStock(
        Event $event,
        $component,
        $stock
    ) {
        if (empty($stock)) {
            $Settings = Settings::findFirst(['columns' => 'stock']);
            return $Settings->stock;
        } else {
            return $stock;
        }
    }
    public function defaultZipCode(
        Event $event,
        $component,
        $zip
    ) {
        if (empty($zip)) {
            $Settings = Settings::findFirst(['columns' => 'zipcode']);
            return $Settings->zipcode;
        } else {
            return $zip;
        }
    }
    public function test(
        Event $event,
        $component,
        $zip
    ) {
        die('success');
    }
}
