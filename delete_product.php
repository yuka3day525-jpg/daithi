<?php
require_once("conn.php");
require_once("func.php");
session_start();

// 管理者以外は削除できない
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}

// POST以外は受け付けない
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit;
}

// CSRFチェック
if (
    !isset($_POST["csrf_token"]) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    exit("不正なアクセスです。");
}

// 商品IDを受け取る
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    exit("商品IDが正しくありません。");
}

// 商品が存在するか確認
$str = "SELECT * FROM products WHERE key_id = :id";

$stmt = $db->prepare($str);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

$kekka = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kekka) {
    exit("商品が見つかりませんでした。");
}

// 商品を非表示
$str = "UPDATE products SET is_active = 0 WHERE key_id = :id";

$stmt = $db->prepare($str);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

// 使用済みトークンを削除
unset($_SESSION["csrf_token"]);

// 管理者画面へ戻る
header("Location: update_products.php?deleted=1");
exit;
?>

