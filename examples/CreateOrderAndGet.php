<?php

namespace Examples;

require_once('vendor/autoload.php');

use Uala\SDK;
use Uala\Error;

try {
    $sdk = new SDK('your_username', 'your_client_id', 'your_client_secret_id', true);

    $order = $sdk->createOrder(10, 'test', 'https://www.google.com/fail', 'https://www.google.com/success');
    echo var_dump($order) . "<hr>";
    $generatedOrder = $sdk->getOrder($order->uuid);
    echo var_dump($generatedOrder) . "<hr>";
} catch (Error $th) {
    var_dump($th);
}
