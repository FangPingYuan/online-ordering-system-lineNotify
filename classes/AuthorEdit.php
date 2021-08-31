<?php

class AuthorEdit
{
    private $db;
    private $check;
    public function __construct($db)
    {
        $this->db = $db;
        $this->check = true;
    }
    //o
    private function checkRole($roleName)
    {
        $data = ['roleName' => $roleName];
        $sql = 'select * from Role where RoleName =:roleName';
        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($data))
            $this->check = false;
        $query = $stmt->fetchAll();
        if (count($query) == 0) {
            return false;
        }
        return true;
    }
    //o
    private function deleteUserRole($userID)
    {
        $data = ['userID' => $userID];
        $sql = 'DELETE FROM User_Role WHERE UserID=:userID';
        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($data))
            $this->check = false;
    }
    //o
    private function deleteRolPer($roleID)
    {
        $data = ['roleID' => $roleID];
        $sql = 'DELETE FROM Role_Permission WHERE RoleID = :roleID';
        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($data))
            $this->check = false;
    }
    //o
    private function insertUserRole($userID, $roleID)
    {
        $data = ['userID' => $userID, 'roleID' => $roleID];
        $sql = 'INSERT INTO User_Role(UserID, RoleID) VALUES(:userID, :roleID)';
        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($data))
            $this->check = false;
    }
    //o
    private function insertRole($roleName)
    {
        $data = ['roleName' => $roleName];
        $sql = 'insert into Role(RoleName) values(:roleName)';
        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($data))
            $this->check = false;
    }

    public function insertRolPer($roleID, $permissionID)
    {
        $data = ['roleID' => $roleID, 'permissionID' => $permissionID];
        $sql = 'INSERT INTO Role_Permission(RoleID, PermissionID) VALUES (:roleID, :permissionID)';
        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($data))
            $this->check = false;
    }

    public function update($users, $roles, $permissions)
    {
        #v = [0] => name [1] => ID 因javascript無dictionary所採用之方法
        foreach ($users as $k => $v) {
            $this->deleteUserRole($v[1]);
            foreach ($roles as $k1 => $v1) {
                if (!$this->checkRole($v1[0])) {
                    $this->insertRole($v1[0]);
                    $roles[$k1][1] = $this->getRoldID($v1[0])[0]['RoleID'];
                }
                $this->insertUserRole($v[1], $roles[$k1][1]);
            }
        }
        foreach ($roles as $k => $v) {
            $this->deleteRolPer($v[1]);
            foreach ($permissions as $k1 => $v1) {
                $this->insertRolPer($v[1], $v1[1]);
            }
        }
        if ($this->check)
            return true;
        return false;
    }

    public function getAllPermission()
    {
        $sql = 'SELECT * from Permission';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll();
        return $query;
    }

    public function getRoldID($roleName)
    {
        $data = ['roleName' => $roleName];
        $sql = 'SELECT RoleID FROM Role WHERE RoleName = :roleName';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        $query = $stmt->fetchAll();
        return $query;
    }

    public function getAllRole()
    {
        $sql = 'SELECT * from Role';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll();
        return $query;
    }
}
