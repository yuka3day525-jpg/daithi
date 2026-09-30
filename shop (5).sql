-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2026-09-25 16:21:46
-- サーバのバージョン： 10.4.27-MariaDB
-- PHP のバージョン: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `shop`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `categories`
--

INSERT INTO `categories` (`id`, `category_name`) VALUES
(1, '石鹸'),
(2, 'ヘアケア'),
(3, 'スキンケア'),
(5, 'ボディケア');

-- --------------------------------------------------------

--
-- テーブルの構造 `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `shipping_name` varchar(100) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `shipping_name`, `shipping_address`, `created_at`, `updated_at`) VALUES
(3, 3, 2400, 'pending', 'ゆか', '千葉県船橋市', '2026-09-22 10:25:14', '2026-09-25 04:47:24'),
(4, 1, 1600, 'pending', 'admin', 'admin', '2026-09-25 02:48:01', '2026-09-25 04:47:53'),
(5, 3, 4255, 'pending', 'yuka', '埼玉県', '2026-09-25 05:51:27', '2026-09-25 05:51:27');

-- --------------------------------------------------------

--
-- テーブルの構造 `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 3, 3, 1, 700),
(2, 3, 8, 1, 1700),
(3, 4, 12, 1, 1600),
(4, 5, 2, 1, 1555),
(5, 5, 11, 1, 2000),
(6, 5, 1, 1, 700);

-- --------------------------------------------------------

--
-- テーブルの構造 `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `dogname` varchar(50) NOT NULL,
  `dog` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `perso` varchar(200) NOT NULL,
  `title` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `img` varchar(255) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `posts`
--

INSERT INTO `posts` (`id`, `username`, `dogname`, `dog`, `gender`, `perso`, `title`, `comment`, `img`, `created_at`) VALUES
(1, 'A', 'はなとしろ', 'トイプードル', 'メス', '人懐っこい、おっとり', 'お誕生日のお祝い🎂', 'はなとしろのお誕生日のお祝いをしました。\nふたりともおめかしして、記念写真もばっちり！\nケーキを前にして少しそわそわしていましたが、最後までいい子に撮影できました。\nこれからも元気に仲良く過ごしてね。', 'image/g1.jpeg', '2026-06-10'),
(3, 'B', 'ポチ', 'トイプードル', 'メス', 'マイペース、甘えんぼ', 'まったりお昼寝タイム', 'お気に入りの場所で、のんびりお昼寝中。\nふかふかの毛布にあごをのせて、すっかりリラックスしています。\nおうちでゆっくり過ごす時間も大好きです。', 'image/g2.jpeg', '2026-06-17'),
(4, 'C', 'たま', 'トイプードル', 'オス', 'おっとり、甘えんぼ', 'さっぱりトリミング✨', '今日はトリミングに行ってきました。\nふわふわに整えてもらって、すっきりかわいくなりました。\n季節感のあるフォトブースでも記念に一枚。\n今回もとってもお利口さんでした。', 'image/g3.jpeg', '2026-06-30'),
(5, 'D', 'さくら', 'トイプードル', 'オス', '元気いっぱい', 'お気に入りのおもちゃ', '今日はおうちでのんびり過ごしました。\nお気に入りのおもちゃを見つけると、夢中になってずっと遊んでいました。\n満足そうな顔をしていて、こちらまで癒やされます。', 'image/g4.jpeg', '2026-07-09'),
(6, 'E', 'マックス', 'トイプードル', 'オス', 'マイペース、おっとり', 'くっついてまったり', '今日はおうちでのんびり過ごしました。\nぴったりくっついて、安心したようにくつろいでいます。\nこのまま動きたくなさそうなくらい、すっかりリラックスモードです。', 'image/g5.jpeg', '2026-07-21'),
(7, 'F', 'あんず', 'トイプードル', 'メス', 'マイペース、おっとり', 'お気に入りのベッドでごろごろ', 'お気に入りのベッドで、のんびりリラックスタイム。\n気づいたらいつもの場所ですっかりくつろいでいました。\nおうちでまったり過ごす時間も大好きです。', 'image/g6.jpeg', '2026-08-06'),
(8, 'G', 'まろ', 'トイプードル', 'メス', '元気いっぱい、人懐っこい', '子犬のころの一枚', 'まだ小さかったころの懐かしい写真です。\n元気いっぱいで、サークルの中でもいつも楽しそうに遊んでいました。\n今見ると、あどけない表情も子犬らしくてかわいいです。', 'image/g7.jpeg', '2026-08-19'),
(9, 'H', 'レオンとさくら', 'トイプードル', 'オス', 'おっとり、マイペース、甘えんぼ', 'ふたりでまったりタイム', '今日はふたりでのんびりおうち時間。\nお気に入りの場所で、それぞれ好きな姿勢でくつろいでいます。\n近くにいるだけで安心するのか、すっかりリラックスモードです。', 'image/g8.jpeg', '2026-09-02'),
(10, 'I', 'ぷに', 'トイプードル', 'メス', 'マイペース、おっとり', '海辺をおさんぽ', '今日は海までお散歩に行ってきました。\n潮風を感じながら、のんびり海沿いを歩きました。\n少し風が強かったけど、いつもと違う景色に興味津々でした。', 'image/g9.jpeg', '2026-09-15'),
(11, 'J', 'ろい', 'トイプードル', 'オス', '甘えんぼ、マイペース', '毛布の中でかくれんぼ', 'お気に入りの毛布にもぐって、すっぽり隠れていました。\nあたたかくて落ち着くのか、このまましばらく出てくる気配なし。\nちらっと見える黒い姿がかわいくて、つい写真を撮ってしまいました。', 'image/g10.jpeg', '2026-09-22'),
(14, 'あ', 'あ', 'あ', 'オス', '甘えんぼ、おっとり', 'あ', 'あああ', 'image/46ef8fdd565c02912e170c244910af30.jpg', '2026-09-24'),
(15, 'あ', 'あ', 'あ', 'オス', 'おっとり、人懐っこい', 'ああああ', 'あ', 'image/6e5d0b1f971ff4b6bfe09418a2ea79bb.png', '2026-09-24');

-- --------------------------------------------------------

--
-- テーブルの構造 `products`
--

CREATE TABLE `products` (
  `key_id` int(11) NOT NULL,
  `category_id` int(3) NOT NULL,
  `product` varchar(20) NOT NULL,
  `product_name` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `price` int(8) NOT NULL,
  `stock` int(3) NOT NULL,
  `image` varchar(200) NOT NULL,
  `g` varchar(20) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `products`
--

INSERT INTO `products` (`key_id`, `category_id`, `product`, `product_name`, `description`, `price`, `stock`, `image`, `g`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 1, '大地の詩', 'ラベンダー石鹸', 'ふんわりと広がる、ラベンダーのやさしく穏やかな香り。\r\n\r\n紫色の可憐な花から感じられる、心ほどけるような香りを楽しみながら、きめ細かな泡で肌をやさしく包み込みます。\r\n\r\n一日の終わりに、ラベンダーの自然な香りに癒されながら、ゆったりとしたバスタイムをお楽しみください。', 700, 4, 'image/soup01.jpg', '100g', '2026-09-18', '2026-09-18', 1),
(2, 1, '生命の雫', '米ぬか&セージ石鹸', '米ぬかのやさしい恵みと、セージのすっきりとした香りを閉じ込めた石鹸です。\r\n\r\nきめ細かな泡が肌をやさしく包み込み、しっとりとなめらかな洗い上がりへ。\r\n\r\n米ぬかの素朴なぬくもりと、セージの清々しい香りで、毎日のバスタイムを心地よく彩ります。', 1555, 314, 'image/27c5d993325c4263.jpg', '450g', '2026-09-18', '2026-09-18', 1),
(3, 1, '大空の風', 'はちみつ石鹸', 'ふんわりと甘く、やさしいはちみつの香りに包まれる石鹸です。\r\n\r\nはちみつの自然な恵みを感じながら、きめ細かな泡で肌をやさしく洗い上げます。\r\n\r\nまるで花畑を吹き抜ける風のような、心地よく穏やかな使い心地をお楽しみください。\r\n', 700, 99, 'image/soup04.jpg', '100g', '2026-09-18', '2026-09-18', 1),
(4, 1, '天空の雫', 'ハスカップ石鹸', '北海道の大地で育ったハスカップの、甘酸っぱく爽やかな魅力を感じられる石鹸です。\r\n\r\nみずみずしい果実を思わせる爽やかな香りと、きめ細かな泡で肌をやさしく包み込みます。\r\n\r\nすっきりとした香りが広がる、清々しく心地よいバスタイムをお楽しみください。', 700, 100, 'image/soup03.jpg', '100g', '2026-09-25', '2026-09-25', 1),
(5, 1, '雨の詩', 'ベルガモット石鹸', '雨上がりの空気を思わせる、ベルガモットの爽やかな香りが広がる石鹸です。\r\n\r\n柑橘を思わせるほのかな甘さと清々しい香りに包まれながら、きめ細かな泡で肌をやさしく洗い上げます。\r\n\r\n雨上がりの澄んだ空気のような、すっきりと心地よいバスタイムをお楽しみください。\r\n', 700, 100, 'image/soup06.jpg', '100g', '2026-09-18', '2026-09-18', 1),
(6, 1, '海原の風', 'ミント抹茶石鹸', 'すっきりとしたミントの清涼感と、抹茶の穏やかな香りを組み合わせた石鹸です。\r\n\r\n爽やかなミントの香りがふわりと広がり、きめ細かな泡で肌をやさしく洗い上げます。\r\n\r\n海辺を吹き抜ける風のような清々しさと、抹茶の落ち着いた雰囲気を楽しめる、心地よい一品です。\r\n', 700, 100, 'image/soup05.jpg', '100g', '2026-09-18', '2026-09-18', 1),
(7, 2, '木漏れ日の詩', 'うるおいヘアケアセット', '髪を洗う時間が、一日の疲れをほどくひとときになるように。\r\n\r\nきめ細かな泡で髪と頭皮をやさしく洗い上げるシャンプーと、毛先までしっとりと整えるコンディショナーのセットです。指を通すたびに感じる、やわらかくなめらかな仕上がりをお楽しみください。\r\n\r\n慌ただしい朝にも、ゆっくり過ごしたい夜にも。木漏れ日のような穏やかさに包まれながら、毎日のヘアケアを心地よい習慣に。', 1600, 100, 'image/hair01.jpg', '各300ml', '2026-09-18', '2026-09-18', 1),
(8, 2, '夕映えの詩', 'なめらかヘアケアセット', '夕暮れのあたたかな光のように、髪にやさしく向き合う時間を。\r\n\r\nシャンプーで髪をすっきりと洗い上げ、コンディショナーで毛先までなめらかに整えるヘアケアセットです。髪の広がりが気になる日も、指どおりのよい、まとまりのある仕上がりへ。\r\n\r\n一日の終わりに、ゆっくりと髪をいたわるバスタイムを。明日の髪に触れるのが少し楽しみになる、穏やかなひとときをお届けします。', 1700, 99, 'image/hair02.jpg', '各300ml', '2026-09-18', '2026-09-18', 1),
(9, 3, '花雫の詩', 'クレンジングオイル', '一日の終わりに、肌と気持ちをゆっくりほどく時間を。\r\n\r\nメイクになじませて洗い流す、毎日のためのクレンジングオイルです。手のひらでやさしく広げながら、慌ただしかった一日を振り返る時間も、少しだけ穏やかに。\r\n\r\nお気に入りの服を脱ぐように、メイクもすっきり落として素肌へ。洗顔から始まる夜のひとときを、心地よくお過ごしください。', 800, 100, 'image/skin01.jpg', '150mL', '2026-09-18', '2026-09-18', 1),
(10, 3, '朝露の詩', 'ジェル洗顔料', '朝露にふれるような、みずみずしい洗顔のひとときを。\r\n\r\n肌の上にやさしく広がるジェルで、汗や皮脂などの汚れを洗い流す洗顔料です。朝の目覚めにはすっきりと、夜の終わりには一日をリセットするような気持ちで。毎日の洗顔を、心地よい時間に変えてくれます。\r\n\r\n顔を洗って、タオルでそっと水気を拭き取ったら、気持ちも新たに。素肌から始まる一日を、軽やかにお楽しみください。', 800, 100, 'image/skin02.jpg', '120g', '2026-09-18', '2026-09-18', 1),
(11, 3, '彩りの詩', '3種の美容液セット', 'その日の肌と気分に合わせて選ぶ、3色の美容液セット。\r\n\r\n青は、乾燥が気になる肌にうるおいを与える保湿ケア。緑は、肌をすこやかに整えたい日のためのコンディショニングケア。ピンクは、うるおいで肌をなめらかに整え、つややかな印象へ導くケア。それぞれ違った心地よさを、毎日のお手入れに取り入れられます。\r\n\r\n今日はどの一本にしよう。鏡の前で自分の肌と向き合う時間が、少し楽しみになるように。朝も夜も、いつものスキンケアに彩りを添えてください。', 2000, 49, 'image/skin03.jpg', '各30mL（計90mL）', '2026-09-18', '2026-09-18', 1),
(12, 3, 'はじまりの詩', 'スキンケアスターターセット', '自分の肌をいたわる習慣を、今日から少しずつ。\r\n\r\n毎日のスキンケアに取り入れやすい、4つのアイテムをひとつにしたセットです。肌を洗って清潔にし、うるおいを与えて、最後はしっとりと整える。何から始めたらいいか迷っている方にも、ひと通りのお手入れを楽しんでいただけます。\r\n\r\n朝の支度にも、一日の終わりの落ち着いた時間にも。ひとつずつ手に取って、自分の肌と向き合うひとときを。「はじまりの詩」と一緒に、心地よいスキンケア習慣を始めませんか。', 1600, 99, 'image/skin04.jpg', '4点セット（各アイテムの容量は要確認）', '2026-09-18', '2026-09-18', 1),
(15, 2, 'a', 'a', 'ssssssssssss', 3, 23, 'image/fd1a0136e9f4b783.jpg', '33', '2026-09-25', '2026-09-25', 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `role` varchar(10) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$u2FabZeNFN4nuhDDPSHuoOG4eghZmSFeNbhZYZjrp5jt57PozOJQu', 'admin', '2026-09-18', '2026-09-18'),
(2, 'user', 'yamada@gmail.com', '$2y$10$//SwIml5wdH1YGNwuZkSI.67jZnK8quav3JCs4H5VLlRr4kI8MQbC', 'user', '2026-09-18', '2026-09-18'),
(3, 'yuka', 'yukamomoko@gmail', '$2y$10$cooAxuod2X3aQ.U7iE7DKOIcXC5DTT9/fWa6T0t7dtMQ79aaGZfaq', 'user', '2026-09-22', '2026-09-22');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- テーブルのインデックス `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`key_id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルの AUTO_INCREMENT `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- テーブルの AUTO_INCREMENT `products`
--
ALTER TABLE `products`
  MODIFY `key_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
