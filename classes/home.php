<?php
class home
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getShopInfo()
    {
        $statement = $this->db->query('SELECT * FROM public."shop" ORDER BY id');
        $row=$statement->fetchAll();
        return $row;
    }
}
