<?php

namespace App\Services;

class Wfp {
    
    private array $settings;
    private string $time;
    
    public function __construct ($settings)
    {
        $this->settings = $settings;
        $this->time = strtotime(date('Y-m-d H:i:s'));
    }
    
    public function getForm($order_id, $price): string
    {
        
        $sing = $this->getSign($order_id, $price);
        
        $str = '<input type="hidden" name="serviceUrl" value="'.$this->settings['serviceUrl'].'"/>';
        $str .= '<input type="hidden" name="merchantAccount" value="'.$this->settings['merchantAccount'].'"/>';
        $str .= '<input type="hidden" name="merchantAuthType" value="'.$this->settings['merchantAuthType'].'"/>';
        $str .= '<input type="hidden" name="merchantDomainName" value="'.$this->settings['merchantDomainName'].'"/>';
        $str .= '<input type="hidden" name="orderReference" value="'.$order_id.'"/>';
        $str .= '<input type="hidden" name="orderDate" value="'.$this->time.'"/>';
        $str .= '<input type="hidden" name="amount" value="'.$price.'"/>';
        $str .= '<input type="hidden" name="currency" value="UAH"/>';
        $str .= '<input type="hidden" name="productName[]" value="'.$this->settings['productName'].'"/>';
        $str .= '<input type="hidden" name="productPrice[]" value="'.$price.'"/>';
        $str .= '<input type="hidden" name="productCount[]" value="1"/>';
        $str .= '<input type="hidden" name="merchantSignature" value="'.$sing.'"/>';
        
        return $str;
        
    }
    
    private function getSign($order_id, $price): string
    {
        
        $data = [
            "merchantAccount" => $this->settings['merchantAccount'],
    		"merchantDomainName" => $this->settings['merchantDomainName'],
    		"orderReference" => $order_id,
    		"orderDate" => $this->time,
    		"amount" => $price,
    		"currency" => "UAH",
    		"productName[]" => $this->settings['productName'],
    		"productCount[]" => "1",
    		"productPrice[]" => $price    
        ];
        
        return hash_hmac("md5", implode(';', $data), $this->settings['key']);
        
    }
    
    public function genResponse($orderReference): array
    {
        $response = [
            'orderReference' => $orderReference,
            'status' => 'accept',
            'time' => strtotime(date('Y-m-d H:i:s')),
        ];
        
        $response['signature'] = $this->getResponseSign($response);

        return $response;
    }
    
    private function getResponseSign($data): string
    {
        $signString = implode(';', $data); // конкатенируем значения через символ ";"
        return hash_hmac("md5",$signString, $this->settings['key']);
    }
    
}