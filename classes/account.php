<?php
class Account
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function getAccount($account, $password)
    {
        
        $sql = "SELECT * FROM User WHERE Account = :account AND Password = :password";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':account', $account, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->execute();
        $query = $stmt->fetchAll();
        return $query;
    }

    public function checkAccount($account, $password)
    {
        $data = $this->getAccount($account, $password);
        return $data;
    }

    public function registAccount($userInfo)
    {
        $sql = 'INSERT INTO User(Password, Email, Name, username) VALUES (:Password, :Email, :Name, :Username)';
        $stmt = $this->db->prepare($sql);
        if($stmt->execute($userInfo))
            return true;
        else
            return false;
    }

    public function readPermission($userID)
    {
        $sql = 'select u.Name, pm.ModelName, pm.Action, pm.Href
                from (((User as u inner join User_Role as ur on u.UserID = ur.UserID)
                        inner join Role_Permission as rp on ur.RoleID = rp.RoleID)
                        inner join Permission as pm on rp.PermissionID = pm.PermissionID
                     )
                where u.UserID = :userID;';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':userID', $userID, PDO::PARAM_STR);
        $stmt->execute();
        $query = $stmt->fetchAll();
        return $query;
    }

    public function getAllUser()
    {
        $sql = 'select u.Name, u.UserID from User as u';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll();
        return $query;
    }
}
