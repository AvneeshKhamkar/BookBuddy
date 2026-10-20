<?php

require __DIR__ . '/razorpay-php-master/Razorpay.php';

use Razorpay\Api\Api;

$keyId = "rzp_test_yourkey";
$keySecret = "your_secret";

$api = new Api($keyId, $keySecret);

echo "Razorpay SDK Loaded Successfully";

?>