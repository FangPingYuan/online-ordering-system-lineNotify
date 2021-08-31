<?php

class creat
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function postShop($shop_name, $shop_phone, $seller_name, $seller_lineid, $shop_password, $shop_intro, $shop_image)
    {
        $sql = 'INSERT INTO public.shop (shop_name, shop_phone, seller_name, seller_lineid, shop_password, shop_intro, shop_image)VALUES (:shop_name, :shop_phone, :seller_name, :seller_lineid, :shop_password, :shop_intro, :shop_image)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':shop_name', $shop_name, PDO::PARAM_STR);
        $stmt->bindValue(':shop_phone', $shop_phone, PDO::PARAM_STR);
        $stmt->bindValue(':seller_name', $seller_name, PDO::PARAM_STR);
        $stmt->bindValue(':seller_lineid', $seller_lineid, PDO::PARAM_STR);
        $stmt->bindValue(':shop_password', $shop_password, PDO::PARAM_STR);
        $stmt->bindValue(':shop_intro', $shop_intro, PDO::PARAM_STR);
        $stmt->bindValue(':shop_image', $shop_image, PDO::PARAM_STR);

        $stmt->execute();
    }
}
