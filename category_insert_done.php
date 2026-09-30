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

// カテゴリー名を受け取る
$category_name = trim($_POST["category_name"] ?? "");

if ($category_name === "") {

    $_SESSION["errors"] = [
        "カテゴリー名を入力してください。"
    ];

    header("Location: category_insert.php");
    exit;
}

// categoriesに登録
$str = "INSERT INTO categories
        (category_name)
        VALUES
        (:category_name)";

$stmt = $db->prepare($str);
$stmt->bindParam(":category_name", $category_name, PDO::PARAM_STR);
$stmt->execute();

unset($_SESSION["csrf_token"]);

header("Location: admin.php?category_inserted=1");
exit;
?>