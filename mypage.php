<?php
require_once("conn.php");
require_once("func.php");
session_start();

// ログインしていなかったらログインページへ
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
// ユーザー情報を取得
$str = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($str);
$stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// 注文履歴を取得
$str = "SELECT * FROM orders
        WHERE user_id = :user_id
        ORDER BY created_at DESC";

$stmt = $db->prepare($str);
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
// 注文商品を取得
foreach ($orders as &$order) {

    $order_id = $order["id"];

    $str = "SELECT order_items.*, products.product_name, products.image
            FROM order_items
            INNER JOIN products
            ON order_items.product_id = products.key_id
            WHERE order_items.order_id = :order_id";

    $stmt = $db->prepare($str);
    $stmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $order["items"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
unset($order);
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
        <h2>MYPAGE</h2>
    </div>
    <div class="mypage-main">
        <!-- ユーザー情報 -->
        <section class="mypage-user">
            <h3>ユーザー情報</h3>
            <div class="mypage-info">
                <p>
                    <span>ユーザー名</span>
                    <?= h($user["name"]) ?>
                </p>

                <p>
                    <span>メールアドレス</span>
                    <?= h($user["email"]) ?>
                </p>
            </div>
        </section>

        <!-- 注文履歴 -->
        <section class="mypage-orders">
            <h3>注文履歴</h3>
            <?php if (empty($orders)): ?>
                <p class="mypage-empty">
                    注文履歴はありません。
                </p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="mypage-order">
                        <div class="order-heading">
                            <h4>
                                注文日：<?= h($order["created_at"]) ?>
                            </h4>
                            <p>
                                合計：<?= number_format($order["total_price"]) ?>円
                            </p>
                        </div>

                        <div class="order-info">
                            <p>
                                <span>注文状態</span>
                                <?= h($order["status"]) ?>
                            </p>
                            <p>
                                <span>お届け先</span>
                                <?= h($order["shipping_address"]) ?>
                            </p>
                        </div>
                        <h4 class="order-item-title">注文商品</h4>
                        <?php foreach ($order["items"] as $item): ?>
                            <div class="mypage-item">
                                <img src="<?= h($item["image"]) ?>"
                                     alt="<?= h($item["product_name"]) ?>">
                                <div class="mypage-item-text">
                                    <h5><?= h($item["product_name"]) ?></h5>
                                    <p>
                                        価格：<?= number_format($item["price"]) ?>円
                                    </p>
                                    <p>
                                        個数：<?= h($item["quantity"]) ?>個
                                    </p>
                                    <p class="item-subtotal">
                                        小計：<?= number_format($item["price"] * $item["quantity"]) ?>円
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>


        <div class="mypage-links">
            <a href="onlineshop.php">
                ← ショップへ戻る
            </a>

            <a href="index.php">
                トップページへ
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

</body>
</html>