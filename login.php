<?php
// POST送信があるかどうかを判断する
// あればデータベースにつないで認証、なければログイン用のフォームを表示
require_once("conn.php");
require_once("func.php");
session_start();
if(isset($_POST["user"]) && isset($_POST["pass"])){
    // echo "POST送信がある（データベースにつなぐ）";
    // POST送信データを変数に代入、データベースにつなぐ、該当テーブルからユーザー名・パスワード取得
    // prepare,bindpalam,execute
    $user = h($_POST["user"]);
    $pass = $_POST["pass"];
    $str = "SELECT * FROM users WHERE name = :name";
    $stmt = $db -> prepare($str);
    $stmt -> bindParam(":name",$user,PDO::PARAM_STR);
    $e = $stmt -> execute();
    $kekka = $stmt->fetch(PDO::FETCH_ASSOC);
    // var_dump($kekka);
    // kekkaが配列ならばパスワード照合へ $kekka == trueと同じ意義
    if($kekka){
        $r = password_verify($pass,$kekka["password"]);
        // var_dump($r);
        if($r){
            // パスワードOK、セッションにユーザー名保存
            $_SESSION["login"] = $kekka["role"];
            $_SESSION["user_id"] = $kekka["id"];
            if (isset($_SESSION["redirect"])) {
                $redirect = $_SESSION["redirect"];
                unset($_SESSION["redirect"]);
                header("Location: " . $redirect);
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        }else{
            $p = "ユーザー名またはパスワードが違います";
        }
    } else{
        $p = "ユーザー名またはパスワードが違います";
    }
} else{
    $p = "";
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
<h2>LOGIN</h2>
</div>

 <div class="login-main">

    <form action="login.php" method="POST" class="login-form">

        <div class="login-field">
            <label for="user">ユーザー名</label>
            <input type="text" name="user" id="user" required>
        </div>

        <div class="login-field">
            <label for="pass">パスワード</label>
            <input type="password" name="pass" id="pass" required>
        </div>

        <?php if (!empty($p)): ?>
            <p class="login-error"><?= h($p) ?></p>
        <?php endif; ?>

        <button type="submit">ログイン</button>

    </form>

    <div class="login-register">
        <p>アカウントをお持ちでない方</p>
        <a href="pass_create.php">新規会員登録はこちら →</a>
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