<?php

class order
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function postShopMenu($id, $shop_name, $arr)
    {
        $sql = 'SELECT num_card FROM public."order" WHERE shop_name = :shop_name ORDER BY num_card';
        $stmt = $this->db->prepare($sql);
        $stmt->bindvalue(':shop_name', $shop_name, PDO::PARAM_STR);
        $stmt->execute();
        $query = $stmt->fetchAll();
        $rownum = $stmt->rowCount();
        if ($rownum != 0) {
            $num_card = $query[$rownum - 1]['num_card'];
        } else {
            $num_card = 0;
        }
        $datetime = date("Y-m-d H:i:s", mktime(date('H') + 8, date('i'), date('s'), date('m'), date('d'), date('Y')));
        for ($i = 0; $i < count($arr); $i++) {
            $sql = 'INSERT INTO public."order"(client_lineid, shop_name, num_card, item, quantity, per_price, status, order_time, customize)VALUES (:line_id, :shop_name, :num_card, :item, :quantity, :price, :prepare, :order_time, :customize);';
            $stmt = $this->db->prepare($sql);
            $stmt->bindvalue(':line_id', $id, PDO::PARAM_STR);
            $stmt->bindvalue(':shop_name', $shop_name, PDO::PARAM_STR);
            $stmt->bindvalue(':num_card', $num_card + 1, PDO::PARAM_STR);
            $stmt->bindvalue(':item', $arr[$i]['item'], PDO::PARAM_STR);
            $stmt->bindvalue(':quantity', $arr[$i]['quantity'], PDO::PARAM_STR);
            $stmt->bindvalue(':price', $arr[$i]['price'], PDO::PARAM_STR);
            $stmt->bindvalue(':prepare', '準備中', PDO::PARAM_STR);
            $stmt->bindvalue(':order_time', $datetime, PDO::PARAM_STR);
            $stmt->bindvalue(':customize', $arr[$i]['customize'], PDO::PARAM_STR);
            $stmt->execute();
        }
    }


    public function getShopOrder($currentID)
    {
        $sql = $this->db->prepare("SELECT * 
        FROM public.order NATURAL JOIN public.shop WHERE seller_lineid=:currentID ORDER BY num_card ASC");
        $sql->bindValue(':currentID', $currentID, PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getClientOrder($currentID)
    {
        $sql = $this->db->prepare("SELECT * FROM public.order WHERE client_lineid=:currentID ORDER BY num_card ASC");
        $sql->bindValue(':currentID', $currentID, PDO::PARAM_STR);
        $sql->execute();
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function notifyOrder($action, $shop_name, $num_card)
    {
        if ($action == "status_avalible") {
            $sql = $this->db->prepare("UPDATE public.order SET status='可取餐' WHERE shop_name=:shopname AND num_card=:numcard ");
            $sql->bindValue(':shopname', $shop_name, PDO::PARAM_STR);
            $sql->bindValue(':numcard', $num_card, PDO::PARAM_STR);

            $sql->execute();
        }

        if ($action == "status_finish") {
            $sql = $this->db->prepare("UPDATE public.order SET status='完成交易' WHERE shop_name=:shopname AND num_card=:numcard ");
            $sql->bindValue(':shopname', $shop_name, PDO::PARAM_STR);
            $sql->bindValue(':numcard', $num_card, PDO::PARAM_STR);

            $sql->execute();
        }

        if ($action == "status_untaken") {
            $sql = $this->db->prepare("UPDATE public.order SET status='未取餐' WHERE shop_name=:shopname AND num_card=:numcard ");
            $sql->bindValue(':shopname', $shop_name, PDO::PARAM_STR);
            $sql->bindValue(':numcard', $num_card, PDO::PARAM_STR);

            $sql->execute();
        }
    }

    public function selectShopid($currentID)
    {
        $sql = $this->db->prepare("SELECT * FROM public.shop WHERE seller_lineid=:currentID");
        $sql->bindValue(':currentID', $currentID, PDO::PARAM_STR);
        $sql->execute();
        $rowcount = $sql->rowCount();

        return $rowcount;
    }
}
