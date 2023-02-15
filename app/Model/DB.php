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

    public function getAuth($user_id): array
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
    
    public function setAuth($user_id, $auth): bool
    {
        $sql = 'UPDATE users SET auth = :auth WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $params = [':id' => $user_id, ':auth' => json_encode($auth)];
        return $stmt->execute($params);
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
    
    public function setUserSettings($data): void
    {

        $payed = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " +".TRIAL." days"));

        $sql = "INSERT INTO users (client_id, client_secret, auth, domain, payed) VALUES (:client_id, :client_secret, :auth, :domain, :payed)";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':client_id' => $data['client_id'],
            ':client_secret' => $data['client_secret'],
            ':auth' => $data['auth'],
            ':domain' => $data['domain'],
            ':payed' => $payed,
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
    
    public function getPayedDate($user_id): string
    {
        $sql = 'SELECT payed FROM users WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id' => $user_id
        ];
        $stmt->execute($params);

        $oldDate = $stmt->fetchColumn();
        
        if(strtotime(date('Y-m-d H:i:s')) > strtotime($oldDate)){
            $oldDate = date('Y-m-d H:i:s');
        }
        
        return $oldDate;
        
    }
    
    public function addPayedPeriod($obj): bool
    {
        $newDate = match ($obj['amount']) {
            '200' => strtotime($obj['date'] . " +30 days"),
            '500' => strtotime($obj['date'] . " +90 days"),
            '900' => strtotime($obj['date'] . " +180 days"),
            '1700' => strtotime($obj['date'] . " +360 days"),
            default => strtotime($obj['date'] . " +3 days"),
        };

        $newDate = date('Y-m-d H:i:s', $newDate);

        $stmt = $this->pdo->prepare("UPDATE users SET payed = :payed WHERE id = :id");
        $params = [
            ':id' => $obj['user_id'],
            ':payed' => $newDate,
        ];
        $stmt->execute($params);


        $stmt = $this->pdo->prepare("UPDATE orders_wfp SET amount = :amount, status = :status, updated = :updated WHERE id = :id");
        $params = [
            ':id' => $obj['order_id'],
            ':amount' => $obj['amount'],
            ':status' => $obj['status'],
            ':updated' => date('Y-m-d H:i:s')
        ];

        return $stmt->execute($params);

    }
    
    public function addWfpOrder($user_id): int
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
    
    public function getWfpOrderById($id): array
    {
        $sql = "SELECT * FROM orders_wfp WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':id' => $id
        ];
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}