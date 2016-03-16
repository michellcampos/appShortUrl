<?php

require_once(__DIR__.'/../../config/database.php');

class UserModel
{
    public $database = null;

    private function getDatabase()
    {
        $database = new databaseConnect(
            '172.17.0.2',
            'root',
            'root',
            'app_shorturl'
        );
        return $database->dbConnection();
    }

    public function getAllUsers()
    {
        $model = new UrlModel();
        return $model->getAllUsers();
    }

    public function getUserByName($name)
    {
        try {
            $sql = "SELECT name FROM user WHERE name = :name";
            $db = self::getDatabase();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('name', $name);
            $stmt->execute();
            $data = $stmt->fetchObject();
            $db = null;
            return $data;

        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }

    }

    public function getUserById($userId)
    {
        try {
            $sql = "SELECT * FROM user WHERE user_id = :user_id";
            $db = self::getDatabase();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('user_id', $userId);
            $stmt->execute();
            $data = $stmt->fetchObject();
            $db = null;
            return $data;

        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function getUrlsFromUserId($userId)
    {
        try {
            $sql = "SELECT * FROM url  WHERE user_id = :id";
            $db = self::getDatabase();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $userId);
            $stmt->execute();
            $data = $stmt->fetchAll();
            $db = null;
            return $data;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function createUser($name)
    {
        try {
            $sql = "INSERT INTO user VALUES(NULL, :name)";
            $db = self::getDatabase();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("name", $name);
            $stmt->execute();
            $db = null;

        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }



    }
}