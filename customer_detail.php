<?php
require_once("conn.php");
require_once("func.php");
session_start();

// 管理者チェック
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}

// 顧客IDを取得
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    exit("顧客IDが正しくありません。");
}


// 顧客情報を取得
$str = "SELECT *
        FROM users
        WHERE id = :id
        AND role != 'admin'";

$stmt = $db->prepare($str);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit("顧客が見つかりませんでした。");
}


// この顧客の注文履歴を取得
$str = "SELECT *
        FROM orders
        WHERE user_id = :user_id
        ORDER BY created_at DESC";

$stmt = $db->prepare($str);
$stmt->bindParam(":user_id", $id, PDO::PARAM_INT);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


// 各注文の商品を取得
foreach ($orders as &$order) {

    $order_id = $order["id"];

    $str = "SELECT
                order_items.*,
                products.product_name,
                products.image
            FROM order_items
            INNER JOIN products
            ON order_items.product_id = products.key_id
            WHERE order_items.order_id = :order_id";

    $stmt = $db->prepare($str);
    $stmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
    $stmt->execute();

    $order["items"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
// $order["items"]という箱を追加しているだけ。ここにproduct情報が入る
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
        <h2>顧客詳細</h2>
    </div>

    <div class="order-detail-page customer-detail-page">

        <!-- 顧客情報 -->
        <section class="order-detail-section">

            <h3>顧客情報</h3>

            <dl class="order-info">

                <div>
                    <dt>顧客ID</dt>
                    <dd>#<?= h($user["id"]) ?></dd>
                </div>

                <div>
                    <dt>名前</dt>
                    <dd><?= h($user["name"]) ?></dd>
                </div>

                <div>
                    <dt>メールアドレス</dt>
                    <dd><?= h($user["email"]) ?></dd>
                </div>

                <div>
                    <dt>登録日</dt>
                    <dd><?= h($user["created_at"]) ?></dd>
                </div>

            </dl>

        </section>


        <!-- 注文履歴 -->
        <section class="order-detail-section">

            <h3>注文履歴</h3>

            <?php if (empty($orders)): ?>

                <div class="customer-order-empty">

                    <i class="fa-solid fa-box-open"></i>

                    <p>注文履歴はありません。</p>

                </div>

            <?php else: ?>

                <p class="order-count">
                    注文履歴：<?= count($orders) ?>件
                </p>

                <?php foreach ($orders as $order): ?>

                    <div class="customer-order">

                        <!-- 注文番号とステータス -->
                        <div class="customer-order-heading">

                            <h4>
                                注文番号 #<?= h($order["id"]) ?>
                            </h4>

                            <?php if ($order["status"] === "pending"): ?>

                                <span class="order-status pending">
                                    注文受付
                                </span>

                            <?php elseif ($order["status"] === "shipped"): ?>

                                <span class="order-status shipped">
                                    発送済み
                                </span>

                            <?php else: ?>

                                <span class="order-status">
                                    <?= h($order["status"]) ?>
                                </span>

                            <?php endif; ?>

                        </div>


                        <!-- 注文情報 -->
                        <dl class="order-info">

                            <div>
                                <dt>注文日時</dt>
                                <dd>
                                    <?= h($order["created_at"]) ?>
                                </dd>
                            </div>

                            <div>
                                <dt>配送先氏名</dt>
                                <dd>
                                    <?= h($order["shipping_name"]) ?>
                                </dd>
                            </div>

                            <div>
                                <dt>配送先住所</dt>
                                <dd>
                                    <?= h($order["shipping_address"]) ?>
                                </dd>
                            </div>

                        </dl>


                        <!-- 注文商品 -->
                        <h5 class="customer-order-subtitle">
                            注文商品
                        </h5>

                        <div class="order-item-list">

                            <?php foreach ($order["items"] as $item): ?>

                                <div class="order-item">

                                    <img
                                        src="<?= h($item["image"]) ?>"
                                        alt="<?= h($item["product_name"]) ?>"
                                        class="order-item-image"
                                    >

                                    <div class="order-item-info">

                                        <p class="order-item-name">
                                            <?= h($item["product_name"]) ?>
                                        </p>

                                        <p class="order-item-price">
                                            <?= number_format($item["price"]) ?>円
                                            ×
                                            <?= h($item["quantity"]) ?>個
                                        </p>

                                    </div>

                                    <p class="order-item-subtotal">
                                        <?= number_format(
                                            $item["price"] * $item["quantity"]
                                        ) ?>円
                                    </p>

                                </div>

                            <?php endforeach; ?>

                        </div>


                        <!-- 合計金額 -->
                        <div class="order-total">

                            <span>合計金額</span>

                            <strong>
                                <?= number_format($order["total_price"]) ?>円
                            </strong>

                        </div>


                        <!-- 注文詳細へのリンク -->
                        <div class="customer-order-link">

                            <a href="order_admin_detail.php?id=<?= h($order["id"]) ?>">
                                注文詳細を見る
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>


        <!-- 戻る -->
        <div class="admin-back">

            <a href="customer_admin.php">
                <i class="fa-solid fa-arrow-left"></i>
                顧客一覧へ戻る
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