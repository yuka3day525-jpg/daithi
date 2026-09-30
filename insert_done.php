<?php
require_once("conn.php");
require_once("func.php");
session_start();

// 管理者以外は登録できない
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}

// POST以外では登録できない
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

// フォームから受け取る
$category_id = filter_input(INPUT_POST, "category_id", FILTER_VALIDATE_INT);
$product = trim($_POST["product"] ?? "");
$product_name = trim($_POST["product_name"] ?? "");
$description = trim($_POST["description"] ?? "");
$price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_INT);
$stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);
$g = trim($_POST["g"] ?? "");

// エラーを入れる配列
$errors = [];


// 入力チェック
if (
    !$category_id ||
    $product === "" ||
    $product_name === "" ||
    $description === "" ||
    $price === false || $price === null || $price < 0 ||
    $stock === false || $stock === null || $stock < 0 ||
    $g === ""
) {
    $errors[] = "入力内容を確認してください。";
}


// -------------------------
// 画像チェック
// -------------------------

if (
    !isset($_FILES["image"]) ||
    $_FILES["image"]["error"] !== UPLOAD_ERR_OK
) {

    $errors[] = "商品画像を選択してください。";

} else {

    $tmp = $_FILES["image"]["tmp_name"];

    // ファイルサイズ上限：5MB
    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        $errors[] = "画像は5MB以下にしてください。";
    }

    // 本当に画像か確認
    $info = getimagesize($tmp);

    if ($info === false) {

        $errors[] = "画像ファイルを選択してください。";

    } else {

        $extensions = [
            IMAGETYPE_JPEG => "jpg",
            IMAGETYPE_PNG => "png",
            IMAGETYPE_WEBP => "webp",
        ];

        if (!isset($extensions[$info[2]])) {
            $errors[] = "対応していない画像形式です。";
        }
    }
}


// エラーがあったら登録画面へ戻る
if (!empty($errors)) {

    $_SESSION["errors"] = $errors;

    header("Location: insert.php");
    exit;
}


// -------------------------
// ここから画像保存
// -------------------------

$extension = $extensions[$info[2]];

$filename = bin2hex(random_bytes(8)) . "." . $extension;

$image = "image/" . $filename;

if (!move_uploaded_file($tmp, $image)) {

    $_SESSION["errors"] = [
        "画像の保存に失敗しました。"
    ];

    header("Location: insert.php");
    exit;
}

// -------------------------
// 商品を登録
// -------------------------

$str = "INSERT INTO products
        (
            category_id,
            product,
            product_name,
            description,
            price,
            stock,
            image,
            g,
            is_active,
            created_at,
            updated_at
        )
        VALUES
        (
            :category_id,
            :product,
            :product_name,
            :description,
            :price,
            :stock,
            :image,
            :g,
            1,
            NOW(),
            NOW()
        )";

$stmt = $db->prepare($str);

$stmt->bindParam(":category_id", $category_id, PDO::PARAM_INT);
$stmt->bindParam(":product", $product, PDO::PARAM_STR);
$stmt->bindParam(":product_name", $product_name, PDO::PARAM_STR);
$stmt->bindParam(":description", $description, PDO::PARAM_STR);
$stmt->bindParam(":price", $price, PDO::PARAM_INT);
$stmt->bindParam(":stock", $stock, PDO::PARAM_INT);
$stmt->bindParam(":image", $image, PDO::PARAM_STR);
$stmt->bindParam(":g", $g, PDO::PARAM_STR);

$stmt->execute();

// 使用済みのCSRFトークンを削除
unset($_SESSION["csrf_token"]);

// 商品一覧へ戻る
header("Location: update_products.php?inserted=1");
exit;
?>