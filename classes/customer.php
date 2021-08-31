<?php
class customer
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function checkCustomer($id)
    {
        $sql = 'SELECT * FROM public."shop" WHERE seller_lineid = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindvalue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $rownum = $stmt->rowCount();
        return $rownum;
    }
}
