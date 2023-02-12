<?php


namespace App\Services;

use App\Model\DB;

class Bitrix extends CRest
{

    public static int $user_id;

    //override method
    protected static function getSettingData() : array
    {
        $db = new DB();
        return $db->getAuth(self::$user_id);
    }

    //override method
    protected static function setSettingData($auth) : bool
    {
        $db = new DB();
        return $db->setAuth(self::$user_id, $auth);
    }


    public static function getAppInfo($auth)
    {
        $getAppInfo = self::query('app.info', [], $auth);
        return $getAppInfo['result'];
    }

    public static function getUsersList($auth)
    {

        $resultUser = self::query('user.get', ['sort' => 'ID', 'order' => 'ASC'], $auth);

        $userCount = $resultUser['total'];

        $result = [];
        if ($userCount >= 50) {

            $result = array_merge($result, $resultUser['result']);

            $count_x = intdiv($userCount, 50);
            $start = 50;
            $x = 0;

            while ($x < $count_x) {

                $resultUser = self::query('user.get', ['sort' => 'ID', 'order' => 'ASC', 'start' => $start], $auth);

                $result = array_merge($result, $resultUser['result']);

                $start += 50;
                $x++;
            }

        } else {

            $result = $resultUser['result'];

        }

        return $result;
    }
    
    public static function createLead($order, $site, $user_id)
    {
        $lead = self::call('crm.lead.add',[
         	'fields' => [
        		"TITLE" => $site . '#' . $order['id'],
        		"NAME" => $order['client_first_name'], 
                "SECOND_NAME" => $order['client_second_name'], 
                "LAST_NAME" => $order['client_last_name'],
        		//"CONTACT_ID" => 2,
        		"STATUS_ID" => "NEW",
        		"COMMENTS" => $order['client_notes'],
        		"OPENED" => "Y",
        		"ASSIGNED_BY_ID" => $user_id,
        		"CURRENCY_ID" => "UAH", 
                "OPPORTUNITY" => preg_replace("/[^0-9]/", '', $order['full_price']),
                "PHONE" => [ [ "VALUE" => $order['phone'], "VALUE_TYPE" => "WORK" ] ]
        	], 'params' => ["REGISTER_SONET_EVENT" => "Y"],
        ]);
        
        return $lead;
    }
    
    public static function setLeadProducts($products, $leadId)
    {
	    
	    foreach ($products as $product){
	        $productPrice = preg_replace("/[^,.0-9]/","",$product['price']);
            $productPrice = (float)str_replace(",",".",$productPrice);
	        $arr[] = ["PRODUCT_ID" => NULL, "PRODUCT_NAME" => $product['name'], "PRICE" => $productPrice, "QUANTITY" => $product['quantity']];
	    }
	    
	    return self::call('crm.lead.productrows.set', ['id' => $leadId, 'rows' => $arr]);
	    
	}

    public static function query($method, $params = [], $auth = []){
        $queryUrl = "https://".$auth["domain"]."/rest/".$method;
        $queryData = http_build_query(array_merge($params, array("auth" => $auth["access_token"])));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, 1);

        return $result;
    }

}