<?php

use App\Model\DB;
use App\Services\Wfp;

require_once '../vendor/autoload.php';
require_once '../config.php';

$client_id = $_REQUEST['id'];
$domain = $_REQUEST['domain'];
if(!$client_id && !$domain){exit;}






try {
    $pdo = new PDO('mysql:host='.SERVERNAME.';dbname='.DBNAME, USERNAME, PASSWORD);
    
    $sql = 'INSERT INTO `orders` (`client_id`, `status`) VALUES (:client_id, :status)';
    
    $params = [
        ':client_id' => $client_id,
        ':status' => 'new'
        ];
        
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $id = $pdo->lastInsertId();
    
    $pdo = NULL;
  
} catch (PDOException $e) {
    file_put_contents(__DIR__ . '/ERROR_SQL.txt', print_r($e->getMessage(), true));
    die();
}

$merchantAccount = 'freelance_user_60e353ac410ee';
$merchantAuthType = 'SimpleSignature';
$merchantDomainName = 'localtech.kr.ua';
$orderReference = $id;
$orderDate = strtotime(date('Y-m-d H:i:s'));
$serviceUrl = 'https://localtech.kr.ua/marketplace/wfp/status.php';

/*
$dataSet1 = [
		"merchantAccount" => $merchantAccount,
		"merchantDomainName" => $merchantDomainName,
		"orderReference" => $orderReference,
		"orderDate" => $orderDate,
		"amount" => "1.00",
		"currency" => "UAH",
		"productName[]" => "Prom_to_Bitrix24 - 2 days",
		"productCount[]" => "1",
		"productPrice[]" => "1.00"
		];
*/

$dataSet200 = [
		"merchantAccount" => $merchantAccount,
		"merchantDomainName" => $merchantDomainName,
		"orderReference" => $orderReference,
		"orderDate" => $orderDate,
		"amount" => "200.00",
		"currency" => "UAH",
		"productName[]" => "Prom_to_Bitrix24 - 30 days",
		"productCount[]" => "1",
		"productPrice[]" => "200.00"
		];

		
$dataSet500 = [
		"merchantAccount" => $merchantAccount,
		"merchantDomainName" => $merchantDomainName,
		"orderReference" => $orderReference,
		"orderDate" => $orderDate,
		"amount" => "500.00",
		"currency" => "UAH",
		"productName[]" => "Prom_to_Bitrix24 - 90 days",
		"productCount[]" => "1",
		"productPrice[]" => "500.00"
		];
		
$dataSet900 = [
		"merchantAccount" => $merchantAccount,
		"merchantDomainName" => $merchantDomainName,
		"orderReference" => $orderReference,
		"orderDate" => $orderDate,
		"amount" => "900.00",
		"currency" => "UAH",
		"productName[]" => "Prom_to_Bitrix24 - 180 days",
		"productCount[]" => "1",
		"productPrice[]" => "900.00"
		];
		
$dataSet1700 = [
		"merchantAccount" => $merchantAccount,
		"merchantDomainName" => $merchantDomainName,
		"orderReference" => $orderReference,
		"orderDate" => $orderDate,
		"amount" => "1700.00",
		"currency" => "UAH",
		"productName[]" => "Prom_to_Bitrix24 - 360 days",
		"productCount[]" => "1",
		"productPrice[]" => "1700.00"
		];
		
$key = "0878ad1c3a4ce1b41febed69e26aa9dcfd9e5105"; //В данном случае используется "Секретный ключ"

//$sign1 = getSign($dataSet1, $key);
$sign200 = getSign($dataSet200, $key);
$sign500 = getSign($dataSet500, $key);
$sign900 = getSign($dataSet900, $key);
$sign1700 = getSign($dataSet1700, $key);


function getSign($dataSet, $key){
    $signString = implode(';', $dataSet); // конкатенируем значения через символ ";"
    $sign = hash_hmac("md5",$signString,$key);
    return $sign; // возвращаем результат
}



?>

<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <script src="//api.bitrix24.com/api/v1/"></script>

    <title>Продление работы!</title>
  </head>
  <body>
      
        <header class='d-flex flex-row bg-info mb-2'>
            <div class="logo">
                <img class='logo_img' src="../img/logo_prom.png" height="100">       
            </div>
            <div class="errors_alert">
                <span id="alert"></span>
            </div> 

        </header>
      
        <h2 class="h2 text-center">Продление работы приложения Prom to Bitrix24!</h2>
        <p class="fs-5 text-center">Стоимость продления не зависит от колличества сайтов prom!</p>
        
        <hr>

        <div class="d-flex justify-content-between">
            
           
            
            <form method="post" action="https://secure.wayforpay.com/pay" accept-charset="utf-8">
                <input type="hidden" name="merchantAccount" value="<?=$merchantAccount?>"/>
	            <input type="hidden" name="merchantAuthType" value="<?=$merchantAuthType?>"/>
	            <input type="hidden" name="merchantDomainName" value="<?=$merchantDomainName?>"/>
	            <input type="hidden" name="orderReference" value="<?=$orderReference?>"/>
	            <input type="hidden" name="orderDate" value="<?=$orderDate?>"/>
	            <input type="hidden" name="amount" value="200.00"/>
	            <input type="hidden" name="currency" value="UAH"/>
	            <input type="hidden" name="productName[]" value="Prom_to_Bitrix24 - 30 days"/>
	            <input type="hidden" name="productPrice[]" value="200.00"/>
	            <input type="hidden" name="productCount[]" value="1"/>
	            <input type="hidden" name="merchantSignature" value="<?=$sign200?>"/>
                <input class="btn btn-success m-2" type="submit" value="200 грн / 30 дней">
            </form>

            <form method="post" action="https://secure.wayforpay.com/pay" accept-charset="utf-8">
                <input type="hidden" name="merchantAccount" value="<?=$merchantAccount?>"/>
	            <input type="hidden" name="merchantAuthType" value="<?=$merchantAuthType?>"/>
	            <input type="hidden" name="merchantDomainName" value="<?=$merchantDomainName?>"/>
	            <input type="hidden" name="orderReference" value="<?=$orderReference?>"/>
	            <input type="hidden" name="orderDate" value="<?=$orderDate?>"/>
	            <input type="hidden" name="amount" value="500.00"/>
	            <input type="hidden" name="currency" value="UAH"/>
	            <input type="hidden" name="productName[]" value="Prom_to_Bitrix24 - 90 days"/>
	            <input type="hidden" name="productPrice[]" value="500.00"/>
	            <input type="hidden" name="productCount[]" value="1"/>
	            <input type="hidden" name="merchantSignature" value="<?=$sign500?>"/>
                <input class="btn btn-success m-2" type="submit" value="500 грн / 90 дней">
            </form>

            <form method="post" action="https://secure.wayforpay.com/pay" accept-charset="utf-8">
                <input type="hidden" name="merchantAccount" value="<?=$merchantAccount?>"/>
	            <input type="hidden" name="merchantAuthType" value="<?=$merchantAuthType?>"/>
	            <input type="hidden" name="merchantDomainName" value="<?=$merchantDomainName?>"/>
	            <input type="hidden" name="orderReference" value="<?=$orderReference?>"/>
	            <input type="hidden" name="orderDate" value="<?=$orderDate?>"/>
	            <input type="hidden" name="amount" value="900.00"/>
	            <input type="hidden" name="currency" value="UAH"/>
	            <input type="hidden" name="productName[]" value="Prom_to_Bitrix24 - 180 days"/>
	            <input type="hidden" name="productPrice[]" value="900.00"/>
	            <input type="hidden" name="productCount[]" value="1"/>
	            <input type="hidden" name="merchantSignature" value="<?=$sign900?>"/>
                <input class="btn btn-success m-2" type="submit" value="900 грн / 180 дней">
            </form>
            
            <form method="post" action="https://secure.wayforpay.com/pay" accept-charset="utf-8">
                <input type="hidden" name="merchantAccount" value="<?=$merchantAccount?>"/>
	            <input type="hidden" name="merchantAuthType" value="<?=$merchantAuthType?>"/>
	            <input type="hidden" name="merchantDomainName" value="<?=$merchantDomainName?>"/>
	            <input type="hidden" name="orderReference" value="<?=$orderReference?>"/>
	            <input type="hidden" name="orderDate" value="<?=$orderDate?>"/>
	            <input type="hidden" name="amount" value="1700.00"/>
	            <input type="hidden" name="currency" value="UAH"/>
	            <input type="hidden" name="productName[]" value="Prom_to_Bitrix24 - 360 days"/>
	            <input type="hidden" name="productPrice[]" value="1700.00"/>
	            <input type="hidden" name="productCount[]" value="1"/>
	            <input type="hidden" name="merchantSignature" value="<?=$sign1700?>"/>
                <input class="btn btn-success m-2" type="submit" value="1700 грн / 360 дней">
            </form>
            
        </div>
        
        <hr>
        
        <input class="btn btn-dark m-3" type="submit" onclick="openApplication()" value="На главную..." />
        
           <hr>
    
    <footer>
    <a href='mailto:localtech.dev@gmail.com?subject="Поддержка пользователя"'>localtech.dev@gmail.com</a>
    <a href="https://t.me/localtech_dev" target="_blank"><img src="../img/telegram.png" alt=""></a>
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
<script>
function openApplication() {
	if(BX24){
        BX24.closeApplication();
	    BX24.openApplication(); 
    } else {
        document.location.href = "http://localtech.kr.ua/home.html";
    }
}

</script>
  </body>
</html>