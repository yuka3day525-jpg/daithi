<?php
require_once("conn.php");
require_once("func.php");
session_start();

// 管理者チェック
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}

// POST以外は禁止
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: update_products.php");
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

// 商品ID
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    exit("商品IDが正しくありません。");
}

// 再販売
$str = "UPDATE products
        SET is_active = 1,
            updated_at = NOW()
        WHERE key_id = :id
        AND is_active = 0";

$stmt = $db->prepare($str);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    exit("対象の販売終了商品が見つかりませんでした。");
}

// CSRFトークン削除
unset($_SESSION["csrf_token"]);

// 商品管理画面へ戻る
header("Location: update_products.php?restarted=1");
exit;
?>