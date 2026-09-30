<?php
require_once("conn.php");
require_once("func.php");
session_start();

$id = h($_GET["id"] ?? 1);

// 商品情報を取得
$str = "SELECT * FROM products WHERE key_id = :id";

$stmt = $db->prepare($str);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

$kekka = $stmt->fetch(PDO::FETCH_ASSOC);

// 商品が見つからなかった場合
if (!$kekka) {
    exit("商品が見つかりませんでした。");
}
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
<h2>PRODUCT DETAIL</h2>
</div>

<div class="product-detail">

    <div class="detail-image">
        <img src="<?= h($kekka["image"]) ?>" alt="<?= h($kekka["product_name"]) ?>">
    </div>

    <div class="detail-text">

        <h2><span><?= h($kekka["product"]) ?></span>  <?= h($kekka["product_name"]) ?></h2>

        <p><?= nl2br(h($kekka["description"])) ?></p>

        <p class="price">
            価格:<?= number_format($kekka["price"]) ?>円
        </p>

        <p>内容量：<?= h($kekka["g"]) ?></p>

        <?php if ($kekka["stock"] > 0 && $kekka["stock"] <= 10): ?>
            <p class="low-stock">
                残りわずか
            </p>
        <?php endif; ?>

        <?php if ($kekka["stock"] > 0): ?>

            <form action="cart.php" method="post">

                <input
                    type="hidden"
                    name="product_id"
                    value="<?= h($kekka["key_id"]) ?>"
                >

                <label for="kosuu">数量</label>

                <input
                    type="number"
                    id="kosuu"
                    name="kosuu"
                    value="1"
                    min="1"
                    max="<?= min(10, $kekka["stock"]) ?>"
                    required
                >

                <button type="submit">
                    カートに入れる
                </button>

            </form>

        <?php else: ?>

            <p class="low-stock">現在、在庫切れです。</p>

        <?php endif; ?>

    </div>

</div>
<a href="javascript:history.back()" class="detail-back">← 戻る</a>


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