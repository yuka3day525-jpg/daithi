<?php
require_once("conn.php");
require_once("func.php");
session_start();

$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["errors"]);

// 管理者チェック
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$id = h($_GET["id"]);

// 商品情報を取得
$str = "SELECT * FROM products WHERE key_id = :id";

$stmt = $db->prepare($str);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

$kekka = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kekka) {
    exit("商品が見つかりませんでした。");
}

$category_list = $db->query("SELECT * FROM categories");
$categories = $category_list->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>大地の詩</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- ↑javaを手打ちしなくても、2,000以上のjQuery プラグインが公開されているサービス -->
     <script src="js/jquery.bgswitcher.js"></script>
    <!-- ↑フリーで配布されてるスライドショーのリンクを直下の専用のファイルに保存するのを忘れない、スライドショー用のプリントのリンク -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kiwi+Maru&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/mystyle.css">
</head>

<body>
<p class="hum">
    <i class="fa-solid fa-bars open" style="color: rgb(3, 142, 100);"></i> 
    <i class="fa-solid fa-xmark close" style="color: rgb(2, 94, 51);"></i>
    <!-- <! open,closeなどのクラスを付けるのを忘れない、fa-xmarkで×アイコン --> 
</p> 
<header>
<div class="top">
<a href="index.php">
<h2>大地の詩</h2>
<p>daichinouta</p>
</a>
</div>
<nav class="site-nav">
<ul>
<li><a href="concept.php"><i class="fa-solid fa-soap"></i>CONCEPT</a></li>
<li><a href="onlineshop.php"><i class="fa-solid fa-basket-shopping"></i>ONLINESHOP</a></li>
<li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i>CART</a></li>
<?php
    if(isset($_SESSION["login"])==false){
        echo "<li><a href=\"login.php\"><i class=\"fa-solid fa-right-to-bracket\"></i>LOGIN</a></li>";
    }
    if(isset($_SESSION["login"]) and $_SESSION["login"] == "admin"){
        echo "<li><a href=\"admin.php\"><i class=\"fa-solid fa-gear\"></i>ADMIN</a></li>";
    }
    if(isset($_SESSION["login"])){
        echo "<li><a href=\"mypage.php\"><i class=\"fa-solid fa-user\"></i>MYPAGE</a></li>";
        echo "<li><a href=\"logout.php\"><i class=\"fa-solid fa-right-from-bracket\"></i>LOGOUT</a></li>";
    }
?>
</ul>
</nav>
</header>


<article class="onlineshop">
<div class="midasi">
<h2>商品編集</h2>
</div>

<div class="product-update">
<?php if (!empty($errors)): ?>
    <div class="error-message">
        <?php foreach ($errors as $error): ?>
            <p><?= h($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<h3>商品情報の編集</h3>

    <form action="update_done.php"
          method="post"
          enctype="multipart/form-data"
          class="product-update-form">

        <input type="hidden"
               name="id"
               value="<?= h($kekka["key_id"]) ?>">

        <!-- 商品の種類 -->
        <div class="update-field">
            <label for="product">商品の種類</label>

            <input type="text"
                   name="product"
                   id="product"
                   value="<?= h($kekka["product"]) ?>"
                   maxlength="20"
                   required>
        </div>

        <!-- 商品名 -->
        <div class="update-field">
            <label for="product_name">商品名</label>

            <input type="text"
                   name="product_name"
                   id="product_name"
                   value="<?= h($kekka["product_name"]) ?>"
                   maxlength="30"
                   required>
        </div>

        <!-- カテゴリー -->
        <div class="update-field">
            <label for="category_id">カテゴリー</label>

            <select name="category_id" id="category_id">

                <?php foreach ($categories as $category): ?>

                    <option
                        value="<?= h($category["id"]) ?>"
                        <?= $category["id"] == $kekka["category_id"] ? "selected" : "" ?>
                    >
                        <?= h($category["category_name"]) ?>
                    </option>

                <?php endforeach; ?>

            </select>
        </div>

        <!-- 商品説明 -->
        <div class="update-field">
            <label for="description">商品説明</label>

            <textarea name="description"
                      id="description"
                      rows="7"
                      required><?= h($kekka["description"]) ?></textarea>
        </div>

        <!-- 価格と在庫 -->
        <div class="update-field-row">

            <div class="update-field">
                <label for="price">価格（円）</label>

                <input type="number"
                       name="price"
                       id="price"
                       value="<?= h($kekka["price"]) ?>"
                       min="0"
                       required>
            </div>

            <div class="update-field">
                <label for="stock">在庫数（個）</label>

                <input type="number"
                       name="stock"
                       id="stock"
                       value="<?= h($kekka["stock"]) ?>"
                       min="0"
                       required>
            </div>

        </div>

        <!-- 内容量 -->
        <div class="update-field">
            <label for="g">内容量</label>

            <input type="text"
                   name="g"
                   id="g"
                   value="<?= h($kekka["g"]) ?>"
                   maxlength="20"
                   required>
        </div>

        <!-- 商品画像 -->
        <div class="update-field">

            <label>現在の商品画像</label>

            <img src="<?= h($kekka["image"]) ?>"
                 alt="<?= h($kekka["product_name"]) ?>"
                 class="update-image">

        </div>

        <div class="update-field">

            <label for="image">画像を変更する</label>

            <input type="file"
                   name="image"
                   id="image"
                   accept="image/*">

            <p class="update-note">
                ※画像を変更しない場合は、選択する必要はありません。
            </p>

        </div>

        <input type="hidden"
               name="csrf_token"
               value="<?= h($_SESSION["csrf_token"]) ?>">

        <button type="submit" class="update-submit">
            <i class="fa-solid fa-check"></i>
            更新する
        </button>

    </form>


    <!-- 削除 -->
    <div class="update-delete-area">

        <h3>商品の削除</h3>

        <p>この商品を販売終了にする場合はこちらから操作してください。</p>

        <form action="delete_product.php"
              method="post"
              onsubmit="return confirm('削除してよろしいですか？')">

            <input type="hidden"
                   name="id"
                   value="<?= h($kekka["key_id"]) ?>">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= h($_SESSION["csrf_token"]) ?>">

            <button type="submit" class="update-delete">
                <i class="fa-solid fa-trash"></i>
                削除する
            </button>

        </form>

    </div>

    <div class="admin-back">
        <a href="update_products.php">
            <i class="fa-solid fa-arrow-left"></i>
            商品一覧へ戻る
        </a>
    </div>

</div>


</article>

<footer>
<small>&copy;Daichi no uta 2023 all rights reserved</small>
</footer>

<script>
$(".hum").click(function () {
$(".site-nav").toggleClass("show");
});
</script>
<script>
$(".hum").click(function () {
$(".hum").toggleClass("show2");
});
</script>
<!-- ↑必ずhtmlの最下層に記入する、全部読んでからプログラムを実行するかららしい、humていうクラスをクリックしたときにnavメニューが表示
一番下がスライドショー、並べ替えなどいろいろ変えられるらしい、該当するところに変えるの忘れないでね -->

</body>
</html>