<?php
header('Content-Type: application/json; charset=UTF-8');


$dbhost = '140.127.74.164';
$dbport = '25432';
$dbname = 'yuyteat';
$dbuser = 'yuytlab';
$dbpassword = '970314970314';
$pdo = new PDO("pgsql: host=$dbhost; port=$dbport; dbname=$dbname", $dbuser, $dbpassword);

@$action =  $_POST["action"];
if ($action == "update_status") {
    @$shop_name = $_POST["shop_name"];
    @$num_card = $_POST["num_card"];

    $sql = $pdo->prepare("UPDATE public.order SET status='可取餐' WHERE shop_name=:shopname AND num_card=:numcard ");
    $sql->bindValue(':shopname', $shop_name, PDO::PARAM_STR);
    $sql->bindValue(':numcard', $num_card, PDO::PARAM_STR);

    $sql->execute();
}

if ($action == "update_untaken") {
    @$shop_name = $_POST["shop_name"];
    @$num_card = $_POST["num_card"];

    $sql = $pdo->prepare("UPDATE public.order SET taken_status='未取餐' WHERE shop_name=:shopname AND num_card=:numcard ");
    $sql->bindValue(':shopname', $shop_name, PDO::PARAM_STR);
    $sql->bindValue(':numcard', $num_card, PDO::PARAM_STR);

    $sql->execute();
}

$sql = $pdo->prepare("SELECT * FROM public.order ORDER BY num_card ASC");
$sql->execute();
$result = $sql->fetchAll(PDO::FETCH_ASSOC);

$pdo = null;
echo json_encode($result);
