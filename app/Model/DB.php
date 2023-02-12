<?php

namespace App\Model;

use PDO;

require_once '../config.php';

class DB
{
    public object $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:host='.SERVERNAME.';dbname='. DBNAME, USERNAME, PASSWORD);
    }

    public function getAuth($user_id)
    {
        $sql = 'SELECT client_id, client_secret, auth FROM users WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $params = [':id' => $user_id];
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $auth = json_decode($result['auth'], 1);
        $auth['C_REST_CLIENT_ID'] = $result['client_id'];
        $auth['C_REST_CLIENT_SECRET'] = $result['client_secret'];

        return $auth;
    }
    
    public function setAuth($user_id, $auth)
    {
        $sql = 'UPDATE users SET auth = :auth WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $params = [':id' => $user_id, ':auth' => json_encode($auth)];
        $result = $stmt->execute($params);

        return $result;
    }
    
    public function getSettings($user_id): array
    {
        $sql = "SELECT * FROM settings WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':user_id' => $user_id,
        ];
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getUsers(): array|bool
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateSettings($data): void
    {
        $sql = "UPDATE settings SET responsible_id = :responsible_id, responsible = :responsible, site = :site, api = :api WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id' => $data['setting_id'],
            ':responsible_id' => $data['responsible_id'],
            ':responsible' => $data['responsible'],
            ':site' => $data['site'],
            ':api' => $data['api'],
        ];
        $stmt->execute($params);
    }

    public function deleteSettings($id): void
    {
        $sql = "DELETE FROM settings WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id' => $id,
        ];
        $stmt->execute($params);
    }
    
    public function setUserSettings($data)
    {
        $sql = "INSERT INTO users (client_id, client_secret, auth, domain) VALUES (:client_id, :client_secret, :auth, :domain)";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':client_id' => $data['client_id'],
            ':client_secret' => $data['client_secret'],
            ':auth' => $data['auth'],
            ':domain' => $data['domain'],
        ];
        $stmt->execute($params);
        
        $user_id = $this->pdo->lastInsertId();
        
        $this->setSettings($user_id, $data);
        
    }
    
    public function setSettings($user_id, $data) : void
    {
        $sql = "INSERT INTO settings (user_id, responsible_id, responsible, site, api) VALUES (:user_id, :responsible_id, :responsible, :site, :api)";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':user_id' => $user_id,
            ':responsible_id' => $data['user_id'],
            ':responsible' => $data['responsible'],
            ':site' => $data['site'],
            ':api' => $data['api'],
        ];
        $stmt->execute($params);
    }

    public function getUserByDomain($domain): array|bool
    {
        $sql = "SELECT * FROM users WHERE domain = :domain";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':domain' => $domain
        ];
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getClientById($id): array
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id' => $id
        ];
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function setOrderLog($data): void
    {
        $sql = "INSERT INTO orders_log (user_id, setting_id, domain, site, order_id) VALUES (:user_id, :setting_id, :domain, :site, :order_id)";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':user_id' => $data['user_id'],
            ':setting_id' => $data['setting_id'],
            ':domain' => $data['domain'],
            ':site' => $data['site'],
            ':order_id' => $data['order_id'],
        ];
        $stmt->execute($params);
    }
    
    public function addWfpOrder($user_id)
    {
        
        $sql = 'INSERT INTO `orders_wfp` (`user_id`, `status`) VALUES (:user_id, :status)';
    
        $params = [
            ':user_id' => $user_id,
            ':status' => 'new'
            ];
            
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$this->pdo->lastInsertId();
        
    }

}