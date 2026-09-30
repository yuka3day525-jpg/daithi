<?php
require_once("conn.php");
require_once("func.php");
session_start();

// $pass1 = random_bytes(16);
// // var_dump($pass1);//文字化け
// $pass2 = bin2hex($pass1);
// // var_dump($pass2);//16進数に変換される
// $_SESSION["token"] = $pass2;

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// ログインしていなかったらログインページへ
if (!isset($_SESSION["login"])) {
    $_SESSION["redirect"] = "order_confirm.php";
    header("Location: login.php");
    exit;
}

// カートが空ならカートページへ
if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    header("Location: cart.php");
    exit;
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
        <h2>ORDER CONFIRM</h2>
    </div>

    <div class="order-confirm-main">

        <h3>ご注文内容の確認</h3>

        <?php
        $total = 0;

        foreach ($_SESSION["cart"] as $key => $item) {

            $product_id = $item["product_id"];
            $kosuu = $item["kosuu"];

            $str = "SELECT * FROM products
                    WHERE key_id = :id AND is_active = 1";

            $stmt = $db->prepare($str);
            $stmt->bindParam(":id", $product_id, PDO::PARAM_INT);
            $stmt->execute();

            $kekka = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$kekka) {
                unset($_SESSION["cart"][$key]);
                continue;
            }

            $product_name = h($kekka["product_name"]);
            $price = $kekka["price"];
            $image = h($kekka["image"]);

            $subtotal = $price * $kosuu;
            $total += $subtotal;
        ?>

            <div class="confirm-item">

                <img src="<?= $image ?>" alt="<?= $product_name ?>">

                <div class="confirm-item-text">

                    <h4><?= $product_name ?></h4>

                    <p>価格：<?= number_format($price) ?>円</p>

                    <p>個数：<?= h($kosuu) ?>個</p>

                    <p class="confirm-subtotal">
                        小計：<?= number_format($subtotal) ?>円
                    </p>

                </div>

            </div>

        <?php
        }
        ?>

        <div class="confirm-total">
            合計：<?= number_format($total) ?>円
        </div>

        <a href="cart.php" class="confirm-back">
            ← カートに戻る
        </a>


        <h3 class="shipping-title">お届け先情報</h3>

        <form action="order.php" method="post" class="confirm-form">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= h($_SESSION["csrf_token"]) ?>">

            <div class="confirm-field">

                <label for="shipping_name">お名前</label>

                <input type="text"
                       name="shipping_name"
                       id="shipping_name"
                       required>

            </div>

            <div class="confirm-field">

                <label for="shipping_address">お届け先住所</label>

                <input type="text"
                       name="shipping_address"
                       id="shipping_address"
                       required>

            </div>

            <button type="submit">
                この内容で注文する →
            </button>

        </form>

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

</body>
</html>