<?php
session_start();
unset($_SESSION['coupon_code']);
$_SESSION['coupon_success'] = 'Coupon removed.';
header('Location: cart.php');
exit();
