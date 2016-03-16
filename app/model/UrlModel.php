<?php

require_once(__DIR__.'/../../config/database.php');

class UrlModel
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
        try {
            $sql = "SELECT * FROM url ORDER BY hits DESC";
            $db = self::getDatabase();
            $stmt = $db->prepare($sql);
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

    public function getUrlById($urlId)
    {
        try {
            $sql = "SELECT * FROM url WHERE url_id = :id";
            $db = self::getDatabase();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $urlId);
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

    public function getUrlByHash($hashUrl)
    {
        try {
            $sql = "SELECT * FROM url WHERE shortUrl = :hashUrl";
            $db = self::getDatabase();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('hashUrl', $hashUrl);
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

    public function createUrl($data)
    {
        try {
            $sql = "INSERT INTO url VALUES(NULL, :user_id, 0, :url, :shortUrl)";
            $db = self::getDatabase();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('user_id', $data['user_id']);
            $stmt->bindParam('url', $data['url']);
            $stmt->bindParam('shortUrl', $data['short_url']);
            $stmt->execute();
            $lastInsertId = $db->lastInsertId();
            $db = null;
            return $lastInsertId;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }

    }

    public function updateHitsByUrlId($urlId)
    {
        try {
            $sql = "UPDATE url SET hits = :hits WHERE url_id = :id";
            $db = self::getDatabase();
            $stmt = $db->prepare($sql);
            $currentHits = self::getUrlById($urlId);
            $hits = $currentHits->hits + 1;
            $stmt->bindParam('id', $urlId);
            $stmt->bindParam('hits', $hits);
            $stmt->execute();
            $db = null;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function getUserById($userId)
    {
        $model = new UserModel();
        return $model->getUserById($userId);
    }
}