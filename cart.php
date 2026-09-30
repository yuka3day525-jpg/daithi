<?php
require_once("conn.php");
require_once("func.php");
session_start();

$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["errors"]);

if (isset($_POST["product_id"]) && isset($_POST["kosuu"])) {

    $product_id = h($_POST["product_id"]); 
    $kosuu = h($_POST["kosuu"]); 

    $found = false;
    if(isset($_SESSION["cart"])){ 

        foreach($_SESSION["cart"] as &$item){

            if($item["product_id"] == $product_id){

                $item["kosuu"] += $kosuu;
                $found = true;
                break;
            }
        }

        unset($item);

        if(!$found){
            $_SESSION["cart"][] = [ 
                "product_id" => $product_id, 
                "kosuu" => $kosuu 
            ]; 
        }

    } else { 
        $_SESSION["cart"] = [ 
            [
                "product_id" => $product_id, 
                "kosuu" => $kosuu 
            ]
        ]; 
    }
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
<h2>CART</h2>
</div>

<?php if (!empty($errors)): ?>

    <div class="error-message">

        <?php foreach ($errors as $error): ?>
            <p><?= h($error) ?></p>
        <?php endforeach; ?>

    </div>

<?php endif; ?>

<div class="cart-main">
<?php
if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    ?>
    <div class="cart-empty">
        <i class="fa-solid fa-basket-shopping"></i>
        <p>カートに商品が入っていません。</p>
        <a href="onlineshop.php">商品を見に行く →</a>
    </div>
    <?php
} else {

    $total = 0;

    foreach ($_SESSION["cart"] as $key => $item) {

        $product_id_cart = $item["product_id"];
        $kosuu_cart = $item["kosuu"];

        $str = "SELECT * FROM products WHERE key_id = :id AND is_active = 1";

        $stmt = $db->prepare($str);

        $stmt->bindParam(":id", $product_id_cart, PDO::PARAM_INT);

        $stmt->execute();

        $kekka = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$kekka) {
            unset($_SESSION["cart"][$key]);
            continue;
        }

        $price = h($kekka["price"]);
        $subtotal = $price * $kosuu_cart;
        $total += $subtotal;

        $image = h($kekka["image"]);
        $product_name = h($kekka["product_name"]);
        ?>

        <div class="cart-item">
            <img src="<?= $image ?>" width="150">

            <div class="cart-text">
            <h3><?= $product_name ?></h3>

            <p>価格：<?= $price ?>円</p>

            <p>個数：<?= $kosuu_cart ?>個</p>

            <p class="cart-subtotal">
                小計：<?= number_format($subtotal) ?>円
            </p>

            <p>
                <a href="delete.php?id=<?= $product_id_cart ?>" class="cart-delete">【削除】</a>
            </p>
            </div>
        </div>

    <?php
    }
    ?>
    <h2 class="cart-total">
    合計：<?= number_format($total) ?>円
    </h2>
    <?php
    }
    ?>

    <?php if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])): ?>

        <div class="cart-actions">

            <a href="onlineshop.php" class="cart-back">
                ショップへ戻る
            </a>

            <a href="order_confirm.php" class="cart-order">
                注文確認へ
            </a>

            <a href="delete.php" class="cart-clear">
                カートを空にする
            </a>

        </div>

    <?php endif; ?>

</div> <!-- cart-mainを閉じる -->
</article> <!-- onlineshopを閉じる -->


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