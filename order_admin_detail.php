<?php
require_once("conn.php");
require_once("func.php");
session_start();

// 管理者チェック
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// 注文IDを受け取る
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    exit("注文IDが正しくありません。");
}


// 注文情報 + 購入者情報を取得
$str = "SELECT
            orders.*,
            users.name,
            users.email
        FROM orders
        INNER JOIN users
        ON orders.user_id = users.id
        WHERE orders.id = :id";

$stmt = $db->prepare($str);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->execute();

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    exit("注文が見つかりませんでした。");
}


// 注文された商品を取得
$str = "SELECT
            order_items.*,
            products.product_name,
            products.image
        FROM order_items
        INNER JOIN products
        ON order_items.product_id = products.key_id
        WHERE order_items.order_id = :order_id";

$stmt = $db->prepare($str);
$stmt->bindParam(":order_id", $id, PDO::PARAM_INT);
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<li><a href="cart.php">CART</a></li>
<?php
    if(isset($_SESSION["login"])==false){
        echo "<li><a href=\"login.php\">LOGIN</a></li>";
    }
    if(isset($_SESSION["login"]) and $_SESSION["login"] == "admin"){
        echo "<li><a href=\"admin.php\">ADMIN</a></li>";
    }
    if(isset($_SESSION["login"])){
        echo "<li><a href=\"mypage.php\">MYPAGE</a></li>";
        echo "<li><a href=\"logout.php\">LOGOUT</a></li>";
    }
?>
</ul>
</nav>
</header>

<article class="onlineshop">

    <div class="midasi">
        <h2>注文管理詳細</h2>
    </div>

    <div class="order-detail-page">

        <!-- 注文情報 -->
        <section class="order-detail-section">

            <h3>注文情報</h3>

            <dl class="order-info">

                <div>
                    <dt>注文番号</dt>
                    <dd>#<?= h($order["id"]) ?></dd>
                </div>

                <div>
                    <dt>注文日時</dt>
                    <dd><?= h($order["created_at"]) ?></dd>
                </div>

                <div>
                    <dt>購入者</dt>
                    <dd><?= h($order["name"]) ?></dd>
                </div>

                <div>
                    <dt>メールアドレス</dt>
                    <dd><?= h($order["email"]) ?></dd>
                </div>

            </dl>

        </section>


        <!-- 配送先 -->
        <section class="order-detail-section">

            <h3>配送先情報</h3>

            <dl class="order-info">

                <div>
                    <dt>配送先氏名</dt>
                    <dd><?= h($order["shipping_name"]) ?></dd>
                </div>

                <div>
                    <dt>配送先住所</dt>
                    <dd><?= h($order["shipping_address"]) ?></dd>
                </div>

            </dl>

        </section>


        <!-- ステータス変更 -->
        <section class="order-detail-section">

            <h3>発送ステータス</h3>

            <form
                action="order_status_update.php"
                method="post"
                class="order-status-form"
            >

                <input
                    type="hidden"
                    name="order_id"
                    value="<?= h($order["id"]) ?>"
                >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= h($_SESSION["csrf_token"]) ?>"
                >

                <div class="order-status-field">

                    <label for="status">
                        現在のステータス
                    </label>

                    <select name="status" id="status">

                        <option
                            value="pending"
                            <?= $order["status"] === "pending" ? "selected" : "" ?>
                        >
                            注文受付
                        </option>

                        <option
                            value="shipped"
                            <?= $order["status"] === "shipped" ? "selected" : "" ?>
                        >
                            発送済み
                        </option>

                    </select>

                </div>

                <button type="submit" class="order-status-submit">
                    <i class="fa-solid fa-check"></i>
                    ステータスを変更する
                </button>

            </form>

        </section>


        <!-- 注文商品 -->
        <section class="order-detail-section">

            <h3>注文商品</h3>

            <div class="order-item-list">

                <?php foreach ($items as $item): ?>

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
                            <?= number_format($item["price"] * $item["quantity"]) ?>円
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

        </section>


        <!-- 戻る -->
        <div class="admin-back">

            <a href="order_admin.php">
                <i class="fa-solid fa-arrow-left"></i>
                注文一覧へ戻る
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