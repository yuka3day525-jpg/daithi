<?php
require_once("conn.php");
require_once("func.php");

session_start();
$errors = [];

if (
    !isset($_POST["csrf_token"]) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    exit("不正なアクセスです。");
}

// ログインしていなかったらログインページへ
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// カートが空ならカートページへ
if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    header("Location: cart.php");
    exit;
}

// POSTされた名前・住所を受け取る
$shipping_name = h($_POST["shipping_name"]);
$shipping_address = h($_POST["shipping_address"]);

// ログイン中のユーザーID
$user_id = $_SESSION["user_id"];

// 合計金額を計算
$total = 0;
foreach ($_SESSION["cart"] as $item) {
    $product_id = $item["product_id"];
    $kosuu = $item["kosuu"];
    $str = "SELECT * FROM products WHERE key_id = :id  AND is_active = 1";
    $stmt = $db->prepare($str);
    $stmt->bindParam(":id", $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $kekka = $stmt->fetch(PDO::FETCH_ASSOC);
    // 商品が削除済み・販売終了
    if (!$kekka) {
        $errors[] = "販売終了の商品がカートに含まれています。";
        continue;
    }

    // 在庫不足
    if ($kosuu > $kekka["stock"]) {
        $errors[] = "在庫が不足している商品があります。";
        continue;
    }
    $price = h($kekka["price"]);
    $subtotal = $price * $kosuu;
    $total += $subtotal;
}

// エラーがあればカートへ戻る
if (!empty($errors)) {

    $_SESSION["errors"] = $errors;

    header("Location: cart.php");
    exit;
}


// ordersに注文を登録
$str = "INSERT INTO orders
        (user_id, total_price, status, shipping_name, shipping_address ,created_at, updated_at)
        VALUES
        (:user_id, :total_price, :status, :shipping_name, :shipping_address ,NOW(),NOW())";

$stmt = $db->prepare($str);
$status = "pending";

$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->bindParam(":total_price", $total, PDO::PARAM_INT);
$stmt->bindParam(":status", $status, PDO::PARAM_STR);
$stmt->bindParam(":shipping_name", $shipping_name, PDO::PARAM_STR);
$stmt->bindParam(":shipping_address", $shipping_address, PDO::PARAM_STR);

$stmt->execute();

// 今登録した注文のIDを取得
$order_id = $db->lastInsertId();


// order_itemsに商品を登録
foreach ($_SESSION["cart"] as $item) {

    $product_id = $item["product_id"];
    $kosuu = $item["kosuu"];

    $str = "SELECT * FROM products WHERE key_id = :id AND is_active = 1";

    $stmt = $db->prepare($str);

    $stmt->bindParam(":id", $product_id, PDO::PARAM_INT);

    $stmt->execute();

    $kekka = $stmt->fetch(PDO::FETCH_ASSOC);

    $price = h($kekka["price"]);

    $str = "INSERT INTO order_items
            (order_id, product_id, quantity, price)
            VALUES
            (:order_id, :product_id, :quantity, :price)";

    $stmt = $db->prepare($str);

    $stmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
    $stmt->bindParam(":product_id", $product_id, PDO::PARAM_INT);
    $stmt->bindParam(":quantity", $kosuu, PDO::PARAM_INT);
    $stmt->bindParam(":price", $price, PDO::PARAM_INT);

    $stmt->execute();

    // 在庫を減らす
    $str = "UPDATE products
            SET stock = stock - :quantity
            WHERE key_id = :id
            AND is_active = 1
            AND stock >= :quantity";

    $stmt = $db->prepare($str);

    $stmt->bindParam(":quantity", $kosuu, PDO::PARAM_INT);
    $stmt->bindParam(":id", $product_id, PDO::PARAM_INT);

    $stmt->execute();
}


// 注文が終わったのでカートを空にする
$_SESSION["cart"] = [];

// 使用済みのCSRFトークンを削除
unset($_SESSION["csrf_token"]);

// 注文完了ページへ
header("Location: order_complete.php");
exit;

?>