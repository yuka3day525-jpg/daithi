<?php
require_once("conn.php");
require_once("func.php");
session_start();

// 管理者チェック
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}

$errors = $_SESSION["errors"] ?? [];
unset($_SESSION["errors"]);

// 検索された名前を取得
$keyword = trim($_GET["keyword"] ?? "");


// 検索されていない場合
if ($keyword === "") {

    $str = "SELECT *
            FROM users
            WHERE role != 'admin'
            ORDER BY created_at DESC";

    $stmt = $db->query($str);

// 名前が検索された場合
} else {

    $str = "SELECT *
            FROM users
            WHERE role != 'admin'
            AND name LIKE :keyword
            ORDER BY created_at DESC";

    $stmt = $db->prepare($str);

    $search_keyword = "%" . $keyword . "%";

    $stmt->bindParam(
        ":keyword",
        $search_keyword,
        PDO::PARAM_STR
    );

    $stmt->execute();
}

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h2>顧客情報</h2>
    </div>

    <div class="order-admin customer-admin">

        <h3>顧客一覧</h3>

        <p class="order-admin-description">
            登録されている顧客の情報を確認できます。
        </p>

        <!-- 顧客検索 -->
        <form action="customer_admin.php"
              method="get"
              class="customer-search">

            <input
                type="text"
                name="keyword"
                value="<?= h($keyword) ?>"
                placeholder="顧客名を検索"
            >

            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
                検索
            </button>

        </form>

        <?php if (empty($users)): ?>

            <!-- 検索結果が0件 -->
            <div class="order-empty">

                <i class="fa-solid fa-users-slash"></i>

                <p>該当する顧客が見つかりませんでした。</p>

                <span>
                    検索条件を変更して、もう一度お試しください。
                </span>

                <a href="customer_admin.php">
                    すべての顧客を見る →
                </a>

            </div>

        <?php else: ?>

            <!-- 顧客数 -->
            <p class="order-count">
                該当する顧客：<?= count($users) ?>件
            </p>

            <!-- 顧客一覧 -->
            <div class="order-table-wrap">

                <table class="order-table">

                    <thead>
                        <tr>
                            <th>顧客ID</th>
                            <th>名前</th>
                            <th>メールアドレス</th>
                            <th>登録日</th>
                            <th>詳細</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($users as $user): ?>

                            <tr>

                                <td>
                                    #<?= h($user["id"]) ?>
                                </td>

                                <td>
                                    <?= h($user["name"]) ?>
                                </td>

                                <td>
                                    <?= h($user["email"]) ?>
                                </td>

                                <td>
                                    <?= h($user["created_at"]) ?>
                                </td>

                                <td>
                                    <a
                                        href="customer_detail.php?id=<?= h($user["id"]) ?>"
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

        <!-- 管理者画面へ戻る -->
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