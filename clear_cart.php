<?php
session_start();
unset($_SESSION['cart']);
unset($_SESSION['coupon_code']);
header("Location: cart.php");
exit();
