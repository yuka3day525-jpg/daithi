<?php

session_start(); 

if (isset($_GET["id"])) {

    // 個別削除
    $product_id = $_GET["id"];

    $new_cart = []; 

    foreach ($_SESSION["cart"] as $item) { 

        if ($item["product_id"] != $product_id) { 
            $new_cart[] = $item; 
        } 
    } 

    $_SESSION["cart"] = $new_cart;

} else {

    // カートを空にする
    $_SESSION["cart"] = [];

}


header("Location: cart.php");
exit;