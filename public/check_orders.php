<?php

use App\Model\DB;
use App\Services\Prom;
use App\Services\Bitrix;

require_once '../vendor/autoload.php';

$db = new DB();

$users = $db->getUsers();

foreach ($users as $user){
    
    $settings = $db->getSettings($user['id']);
    
    if(empty($settings)){continue;}
    
    Bitrix::$user_id = $user['id'];
    
    foreach ($settings as $setting){
        
        $prom = new Prom($setting['api']);
        
        $orders = $prom->getPendingOrders();

        file_put_contents('orders.txt', print_r($orders, true));
        
        if(!empty($orders['orders'])){

            $ids = [];
            
            foreach ($orders['orders'] as $order){
                
                $lead = Bitrix::createLead($order, $setting['site'], $setting['responsible_id']);
                
                $products = Bitrix::setLeadProducts($order['products'], $lead['result']);
                
                $log = [
                    'user_id' => $user['id'],
                    'setting_id' => $setting['id'],
                    'domain' => $user['domain'],
                    'site' => $setting['site'],
                    'order_id' => $order['id'],    
                ];
                $db->setOrderLog($log);
                
                if($lead['result'] && $products['result']){
                    $ids[] = (int)$order['id'];
                }
                
            }
            
            $result = $prom->receiveOrder($ids);
            file_put_contents('log.txt', print_r($result, true));
            
        }
        
    }
    
}


