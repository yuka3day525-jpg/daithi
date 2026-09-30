<?php
$pass = password_hash('tanuki1234',PASSWORD_DEFAULT);
var_dump($pass);

echo "<hr>";

$pass = password_hash('user12345',PASSWORD_DEFAULT);
var_dump($pass);

// [
//     {
//         "name": "admin",
//         "password": "$2y$10$u2FabZeNFN4nuhDDPSHuoOG4eghZmSFeNbhZYZjrp5jt57PozOJQu"
//     },
//     {
//         "name": "user",
//         "password": "$2y$10$//SwIml5wdH1YGNwuZkSI.67jZnK8quav3JCs4H5VLlRr4kI8MQbC"
//     }
// ]

$hash = '$2y$10$//SwIml5wdH1YGNwuZkSI.67jZnK8quav3JCs4H5VLlRr4kI8MQbC';

$p = password_verify("user12345", $hash);
var_dump($p);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>