<?php


namespace App\Services;

define('HOST', 'my.prom.ua/api/v1/');  // e.g.: my.prom.ua, my.tiu.ru, my.satu.kz, my.deal.by, my.prom.md

class Prom
{

    private string $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function getProductList()
    {
        $url = 'products/list?limit=1';
        return $this->send('GET', $url);
    }
    
    public function getPendingOrders()
    {
        $url = 'orders/list';
        //$url .= '?'.http_build_query(['date_from' => $date_from]);
        $url .= '?'.http_build_query(['status' => 'pending']);
        return $this->send('GET', $url);
    }
    
    public function receiveOrder($ids)
    {
        $status = 'received';
        $url = 'orders/set_status';
        $body = [
            "status" => $status,
            "ids" => $ids,  
        ];
        return $this->send('POST', $url, $body);
    }


    private function send($method, $url, $body = null) {
        $headers = array (
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . HOST . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (strtoupper($method) == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

}