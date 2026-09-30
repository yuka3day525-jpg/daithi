<?php
require_once("conn.php");
require_once("func.php");
session_start();

// 管理者以外は更新できない
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}

// POST以外では更新できない
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin.php");
    exit;
}

if (
    !isset($_POST["csrf_token"]) ||
    !isset($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    exit("不正なアクセスです。");
}

// フォームから受け取る
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
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
    !$id ||
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

// 現在の商品情報を取得
$str = "SELECT * FROM products WHERE key_id = :id";

$stmt = $db->prepare($str);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

$kekka = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kekka) {
    exit("商品が見つかりませんでした。");
}

// 画像を変更しない場合は現在の画像を使う
$image = $kekka["image"];

// 新しい画像が選択された場合
if (
    isset($_FILES["image"]) &&
    $_FILES["image"]["error"] === UPLOAD_ERR_OK
) {
    $tmp = $_FILES["image"]["tmp_name"];

    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        $errors[] = "画像は5MB以下にしてください。";
    }

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

} elseif (
    isset($_FILES["image"]) &&
    $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
) {
    $errors[] = "画像のアップロードに失敗しました。";
}


// ここで一度エラー確認
if (!empty($errors)) {

    $_SESSION["errors"] = $errors;

    header("Location: update.php?id=" . $id);
    exit;
}


// エラーがなく、新しい画像が選択されていたら保存
if (
    isset($_FILES["image"]) &&
    $_FILES["image"]["error"] === UPLOAD_ERR_OK
) {
    $extension = $extensions[$info[2]];

    $filename = bin2hex(random_bytes(8)) . "." . $extension;

    $image = "image/" . $filename;

    if (!move_uploaded_file($tmp, $image)) {

        $_SESSION["errors"] = [
            "画像の保存に失敗しました。"
        ];

        header("Location: update.php?id=" . $id);
        exit;
    }
}
// 商品情報を更新
$str = "UPDATE products
        SET category_id = :category_id,
            product = :product,
            product_name = :product_name,
            description = :description,
            price = :price,
            stock = :stock,
            image = :image,
            g = :g
        WHERE key_id = :id";

$stmt = $db->prepare($str);

$stmt->bindParam(":category_id", $category_id, PDO::PARAM_INT);
$stmt->bindParam(":product", $product, PDO::PARAM_STR);
$stmt->bindParam(":product_name", $product_name, PDO::PARAM_STR);
$stmt->bindParam(":description", $description, PDO::PARAM_STR);
$stmt->bindParam(":price", $price, PDO::PARAM_INT);
$stmt->bindParam(":stock", $stock, PDO::PARAM_INT);
$stmt->bindParam(":image", $image, PDO::PARAM_STR);
$stmt->bindParam(":g", $g, PDO::PARAM_STR);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);

$stmt->execute();

// 使用済みのCSRFトークンを削除
unset($_SESSION["csrf_token"]);

// 管理者画面へ戻る
header("Location: update_products.php?updated=1");
exit;
?>