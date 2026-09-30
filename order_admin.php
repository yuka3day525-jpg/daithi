<?php
require_once("conn.php");
require_once("func.php");
session_start();

// 管理者チェック
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}

// 選択されたステータス
$status = $_GET["status"] ?? "";
// 選択された並び順
$sort = $_GET["sort"] ?? "desc";

// 昇順・降順
if ($sort === "asc") {
    $order = "ASC";
} else {
    $order = "DESC";
}

// ステータスが選択されていない場合
if ($status === "") {
    $str = "SELECT
                orders.id,
                orders.total_price,
                orders.status,
                orders.created_at,
                users.name
            FROM orders
            INNER JOIN users
            ON orders.user_id = users.id
            ORDER BY orders.created_at $order";
    $stmt = $db->query($str);

} else {
    $str = "SELECT
                orders.id,
                orders.total_price,
                orders.status,
                orders.created_at,
                users.name
            FROM orders
            INNER JOIN users
            ON orders.user_id = users.id
            WHERE orders.status = :status
            ORDER BY orders.created_at $order";

    $stmt = $db->prepare($str);
    $stmt->bindParam(":status", $status, PDO::PARAM_STR);
    $stmt->execute();
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h2>注文管理</h2>
    </div>

    <div class="order-admin">

        <h3>注文一覧</h3>

        <p class="order-admin-description">
            注文状況の確認や、発送状況の管理ができます。
        </p>

        <!-- 絞り込み・並び替え -->
        <form action="order_admin.php"
              method="get"
              class="order-filter">

            <div class="order-filter-item">
                <label for="status">注文状況</label>

                <select name="status" id="status">

                    <option value=""
                        <?= $status === "" ? "selected" : "" ?>>
                        すべて
                    </option>

                    <option value="pending"
                        <?= $status === "pending" ? "selected" : "" ?>>
                        注文受付
                    </option>

                    <option value="shipped"
                        <?= $status === "shipped" ? "selected" : "" ?>>
                        発送済み
                    </option>

                </select>
            </div>

            <div class="order-filter-item">
                <label for="sort">並び順</label>

                <select name="sort" id="sort">

                    <option value="desc"
                        <?= $sort === "desc" ? "selected" : "" ?>>
                        新しい順
                    </option>

                    <option value="asc"
                        <?= $sort === "asc" ? "selected" : "" ?>>
                        古い順
                    </option>

                </select>
            </div>

            <button type="submit" class="order-filter-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
                表示
            </button>

        </form>

        <?php if (empty($orders)): ?>

            <!-- 注文が0件の場合 -->
            <div class="order-empty">

                <i class="fa-solid fa-box-open"></i>

                <p>該当する注文がありません。</p>

                <span>
                    検索条件を変更して、もう一度お試しください。
                </span>

                <a href="order_admin.php">
                    すべての注文を見る →
                </a>

            </div>

        <?php else: ?>

            <!-- 注文件数 -->
            <p class="order-count">
                該当する注文：<?= count($orders) ?>件
            </p>

            <!-- 注文一覧 -->
            <div class="order-table-wrap">

                <table class="order-table">

                    <thead>
                        <tr>
                            <th>注文番号</th>
                            <th>購入者</th>
                            <th>注文日時</th>
                            <th>合計金額</th>
                            <th>ステータス</th>
                            <th>詳細</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($orders as $order): ?>

                            <tr>

                                <td>
                                    #<?= h($order["id"]) ?>
                                </td>

                                <td>
                                    <?= h($order["name"]) ?>
                                </td>

                                <td>
                                    <?= h($order["created_at"]) ?>
                                </td>

                                <td>
                                    <?= number_format($order["total_price"]) ?>円
                                </td>

                                <td>
                                    <?php if ($order["status"] === "pending"): ?>

                                        <span class="order-status pending">
                                            注文受付
                                        </span>

                                    <?php elseif ($order["status"] === "shipped"): ?>

                                        <span class="order-status shipped">
                                            発送済み
                                        </span>

                                    <?php else: ?>

                                        <?= h($order["status"]) ?>

                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a
                                        href="order_admin_detail.php?id=<?= h($order["id"]) ?>"
                                        class="order-detail-btn"
                                    >
                                        詳細を見る
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

        <div class="admin-back">
            <a href="admin.php">
                <i class="fa-solid fa-arrow-left"></i>
                管理者画面へ戻る
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