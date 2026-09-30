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
    header("Location: order_admin.php");
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

$order_id = filter_input(
    INPUT_POST,
    "order_id",
    FILTER_VALIDATE_INT
);

$status = $_POST["status"] ?? "";


// 注文IDチェック
if (!$order_id) {
    exit("注文IDが正しくありません。");
}


// 許可するステータスだけにする
$allowed_status = [
    "pending",
    "shipped"
];

if (!in_array($status, $allowed_status, true)) {
    exit("ステータスが正しくありません。");
}


// ステータス更新
$str = "UPDATE orders
        SET status = :status,
            updated_at = NOW()
        WHERE id = :id";

$stmt = $db->prepare($str);

$stmt->bindParam(":status", $status, PDO::PARAM_STR);
$stmt->bindParam(":id", $order_id, PDO::PARAM_INT);

$stmt->execute();


// CSRFトークン削除
unset($_SESSION["csrf_token"]);


// 注文詳細へ戻る
header("Location: order_admin_detail.php?id=" . $order_id);
exit;
?>