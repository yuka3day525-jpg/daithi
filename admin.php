<?php
require_once("conn.php");
require_once("func.php");
session_start();

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== "admin") {
    header("Location: index.php");
    exit;
}
$kekka_item = $db -> query("SELECT COUNT(*) FROM products");
$count = $kekka_item -> fetch(PDO::FETCH_ASSOC);

$list = $db -> query("SELECT * FROM categories c,products p WHERE c.id = p.category_id");
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
<h2>ADMIN</h2>
</div>

<div class="admin-main">

    <?php if (isset($_GET["category_inserted"])): ?>
        <p class="update-message">カテゴリーを登録しました</p>
    <?php endif; ?>

    <h3>管理メニュー</h3>

    <div class="admin-menu">

        <a href="update_products.php">
            <i class="fa-solid fa-pen-to-square"></i>
            <div>
                <h4>商品編集</h4>
                <p>登録済み商品の情報を編集します。</p>
            </div>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a href="insert.php">
            <i class="fa-solid fa-circle-plus"></i>
            <div>
                <h4>商品登録</h4>
                <p>新しい商品を登録します。</p>
            </div>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a href="category_insert.php">
            <i class="fa-solid fa-tags"></i>
            <div>
                <h4>カテゴリー登録</h4>
                <p>商品のカテゴリーを追加します。</p>
            </div>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a href="inactive_products.php">
            <i class="fa-solid fa-box-archive"></i>
            <div>
                <h4>販売終了商品</h4>
                <p>販売終了商品の確認・管理を行います。</p>
            </div>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a href="order_admin.php">
            <i class="fa-solid fa-clipboard-list"></i>
            <div>
                <h4>注文管理</h4>
                <p>注文内容や注文状況を確認します。</p>
            </div>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a href="customer_admin.php">
            <i class="fa-solid fa-users"></i>
            <div>
                <h4>顧客情報</h4>
                <p>登録済みの会員情報を確認します。</p>
            </div>
            <i class="fa-solid fa-chevron-right"></i>
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

<!-- 注文管理☑
管理者が注文一覧を見られる
注文番号、購入者、注文日時、金額、ステータスを表示
注文詳細で order_items の商品を見る
pending → 発送済み に変更できる

販売終了商品の再販売☑
is_active = 0 の商品も管理画面には表示
「販売終了」と表示
「再販売する」ボタン
押したら is_active = 1

顧客情報☑
users 一覧
名前・メールアドレス・登録日
「詳細」を押す
その人の注文履歴を表示
さらに注文ごとの商品・数量・購入時価格も表示

表示方法を整理、カテゴリー順にする,いろいろ並び替える☑
ステータスがpendingのときは在庫を消さない☑
入れられるカート数をストック数にする☑
exitエラーをセッションに全部まとめる☑

CSS

9/25 マーケティング　HTMLの本63ページ
メール送信すると返してくれるやつ

google アナリティクス⇒アクセス解析　pfに入れる
google サーチコンソール⇒サイトに来た人の検索ワード調査
google 広告
google タブマネージャー
-->
