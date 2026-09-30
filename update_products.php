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

// カテゴリー一覧を取得
$category_list = $db->query("SELECT * FROM categories");
$categories = $category_list->fetchAll(PDO::FETCH_ASSOC);

// 選択されたカテゴリーID
$category_id = filter_input(
    INPUT_GET,
    "category_id",
    FILTER_VALIDATE_INT
);

$sort = h($_GET["sort"] ?? "new");

if ($sort === "low") {
    $order = "p.price ASC";

} elseif ($sort === "high") {
    $order = "p.price DESC";

} else {
    $order = "p.key_id DESC";
}

// 検索文字を取得
$keyword = trim($_GET["keyword"] ?? "");

if ($keyword === "" && !$category_id) {

    // 検索もカテゴリー選択もしていない
    $list = $db->query(
        "SELECT * FROM categories c, products p
         WHERE c.id = p.category_id
         AND p.is_active = 1
         ORDER BY $order"
    );

} elseif ($keyword === "" && $category_id) {

    // カテゴリーだけ選択した
    $str = "SELECT * FROM categories c, products p
            WHERE c.id = p.category_id
            AND p.is_active = 1
            AND p.category_id = :category_id
            ORDER BY $order";

    $stmt = $db->prepare($str);

    $stmt->bindParam(
        ":category_id",
        $category_id,
        PDO::PARAM_INT
    );

    $stmt->execute();

    $list = $stmt;

} else {

    // キーワード検索
    $str = "SELECT * FROM categories c, products p
            WHERE c.id = p.category_id
            AND p.is_active = 1
            AND p.product_name LIKE :keyword
            ORDER BY $order";

    $stmt = $db->prepare($str);

    $search_keyword = "%" . $keyword . "%";

    $stmt->bindParam(
        ":keyword",
        $search_keyword,
        PDO::PARAM_STR
    );

    $stmt->execute();

    $list = $stmt;
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
<h2>編集</h2>
</div>
<?php
$message = "";

if (isset($_GET["updated"])) {
    $message = "商品情報を更新しました。";
} elseif (isset($_GET["deleted"])) {
    $message = "商品の販売を終了しました。";
} elseif (isset($_GET["inserted"])) {
    $message = "商品情報を登録しました。";
} elseif (isset($_GET["restarted"])) {
    $message = "商品の販売を再開しました。";
}
?>

<?php if ($message !== ""): ?>
    <div class="update-message">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= h($message) ?></span>
    </div>
<?php endif; ?>

<div class="sukoyaka">
<p class="sukoyaka-midasi">商品検索</p>
<div class="category-menu">
    <a href="update_products.php">ALL</a>

    <?php foreach ($categories as $category): ?>
        <a href="update_products.php?category_id=<?= h($category["id"]) ?>">
            <?= h($category["category_name"]) ?>
        </a>
    <?php endforeach; ?>
</div>
<form action="update_products.php" method="get" class="sort-form">
    <?php if ($category_id): ?>
        <input type="hidden"
               name="category_id"
               value="<?= h($category_id) ?>">
    <?php endif; ?>
    <select name="sort">
         <option value="new"
            <?= $sort === "new" ? "selected" : "" ?>>
            新着順
        </option>

        <option value="low"
            <?= $sort === "low" ? "selected" : "" ?>>
            価格が安い順
        </option>

        <option value="high"
            <?= $sort === "high" ? "selected" : "" ?>>
            価格が高い順
        </option>
    </select>
    <button type="submit">並び替え</button>
</form>

<form action="update_products.php" method="get" class="search-form">
    <input type="text"
           name="keyword"
           placeholder="商品名を検索">

    <button type="submit">検索</button>
</form>
</div>
<div class="shop-main">

    <?php
    $ary = $list->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (empty($ary)): ?>

        <!-- 検索結果が0件の場合 -->
        <div class="shop-empty">

            <i class="fa-solid fa-magnifying-glass"></i>

            <p>該当する商品が見つかりませんでした。</p>

            <span>検索条件を変更して、もう一度お試しください。</span>

            <a href="update_products.php">
                すべての商品を見る →
            </a>

        </div>

    <?php else: ?>
        <!-- 商品一覧 -->
        <?php foreach ($ary as $val): ?>

            <div class="shop-section">

                <div class="shop-text">

                    <p class="conduct">
                        <?= h($val["product"]) ?>
                        <span><?= h($val["category_name"]) ?></span>
                    </p>

                    <p class="nedan">
                        <?= h($val["product_name"]) ?>
                        <span>￥<?= number_format($val["price"]) ?>円</span>
                    </p>

                </div>

                <img
                    src="<?= h($val["image"]) ?>"
                    alt="<?= h($val["product_name"]) ?>"
                    class="shop-image"
                >

                <!-- 在庫数 -->
                <p class="admin-stock">
                    在庫：<?= h($val["stock"]) ?>個
                </p>

                <!-- 在庫状況 -->
                <?php if ($val["stock"] <= 0): ?>

                    <p class="low-stock">在庫切れです</p>

                <?php elseif ($val["stock"] <= 10): ?>

                    <p class="low-stock">
                        在庫が少なくなっています
                    </p>

                <?php endif; ?>

                <!-- 編集ボタン -->
                <a
                    href="update.php?id=<?= h($val["key_id"]) ?>"
                    class="product-edit-btn"
                >
                    <i class="fa-solid fa-pen-to-square"></i>
                    商品を編集する
                </a>

            </div>

        <?php endforeach; ?>
    <?php endif; ?>
</div>
<div class="admin-back">
    <a href="admin.php">
        <i class="fa-solid fa-arrow-left"></i>
        管理者画面へ戻る
    </a>
</div>
</article>
<!-- あとで詳細画面を埋め込む -->

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