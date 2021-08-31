<?php

$dbhost = '140.127.74.164';
$dbport = '25432';
$dbname = 'yuyteat';
$dbuser = 'yuytlab';
$dbpassword = '970314970314';
$pdo = new PDO("pgsql: host=$dbhost; port=$dbport; dbname=$dbname", $dbuser, $dbpassword);

@$shop_name = $_POST["shop_name"];
@$shop_phone = $_POST["shop_phone"];
@$seller_name = $_POST["seller_name"];
@$seller_lineid = $_POST["seller_lineid"];
@$shop_password = $_POST["shop_password"];
@$shop_intro = $_POST["shop_intro"];
@$shop_image = $_POST["shop_image"];

$sql = $pdo->prepare('INSERT INTO public.shop
(shop_name, shop_phone, seller_name, seller_lineid, shop_password, shop_intro, shop_image)
VALUES (:shop_name, :shop_phone, :seller_name, :seller_lineid, :shop_password, :shop_intro, :shop_image)');

$sql->bindValue(':shop_name', $shop_name, PDO::PARAM_STR);
$sql->bindValue(':shop_phone', $shop_phone, PDO::PARAM_STR);
$sql->bindValue(':seller_name', $seller_name, PDO::PARAM_STR);
$sql->bindValue(':seller_lineid', $seller_lineid, PDO::PARAM_STR);
$sql->bindValue(':shop_password', $shop_password, PDO::PARAM_STR);
$sql->bindValue(':shop_intro', $shop_intro, PDO::PARAM_STR);
$sql->bindValue(':shop_image', $shop_image, PDO::PARAM_STR);

$sql->execute();

$pdo = null;

?>
