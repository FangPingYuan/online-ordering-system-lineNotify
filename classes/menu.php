<?php
class menu
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function hotItem($shopName){
	    $sql = $this->db->query('SELECT MAX(maxSum) FROM public.order WHERE (maxSum = (SELECT SUM(quantity) FROM public.order GROUP BY item)) and shop_name = '.$shopName);
	    $row = $sql->fetchAll();
	    return $row;
    }

    public function getShopMenu($id){
        $statement = $this->db->query('SELECT * FROM public."Menu" WHERE s_id = '.$id);
        $row=$statement->fetchAll();
        return $row;
    }

    public function postShopMenu($id, $name, $customize, $price){
        $sql = 'INSERT INTO public."Menu"(s_id, name, customize, price)VALUES (:sid, :name, :customize, :price)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindvalue(':sid', $id, PDO::PARAM_STR);
        $stmt->bindvalue(':name', $name, PDO::PARAM_STR);
        $stmt->bindvalue(':customize', $customize, PDO::PARAM_STR);
        $stmt->bindvalue(':price', $price, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    }

    public function getOwnShopMenu($sid)
    {
        $sql = 'SELECT * FROM public."shop" WHERE seller_lineid = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindvalue(':id', $sid, PDO::PARAM_STR);
        $stmt->execute();
	$query = $stmt->fetchAll();
	$sql2 = 'SELECT * FROM public."Menu" WHERE s_id = :id2';
	$stmt2 = $this->db->prepare($sql2);
	$stmt2->bindValue(':id2', $query[0]['id'], PDO::PARAM_INT);
	$stmt2->execute();
	$row=$stmt2->fetchAll();
        return $row;
    }

    public function postOwnShopMenu($id, $name, $customize, $price)
    {
        $sql = 'SELECT * FROM public."shop" WHERE seller_lineid = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindvalue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $query = $stmt->fetchAll();
        $sql = 'INSERT INTO public."Menu"(s_id, name, customize, price)VALUES (:sid, :name, :customize, :price)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindvalue(':sid', $query[0]['id'], PDO::PARAM_INT);
        $stmt->bindvalue(':name', $name, PDO::PARAM_STR);
        $stmt->bindvalue(':customize', $customize, PDO::PARAM_STR);
        $stmt->bindvalue(':price', $price, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    }

    public function getShopMenuCustomize($s_id, $m_id)
    {
        $sql = 'SELECT * FROM public."Menu" WHERE s_id = :s_id AND m_id = :m_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindvalue(':s_id', $s_id, PDO::PARAM_STR);
        $stmt->bindvalue(':m_id', $m_id, PDO::PARAM_STR);
        $stmt->execute();
        $query = $stmt->fetchAll();
        return $query;
    }

    public function deleteShopMenu($id)
    {
        $sql = 'DELETE FROM public."Menu" WHERE m_id = :id;';
        $stmt = $this->db->prepare($sql);
        $stmt->bindvalue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    }
}
