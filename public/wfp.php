<?php

use App\Model\DB;
use App\Services\Wfp;

require_once '../vendor/autoload.php';
require_once '../config.php';

$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

file_put_contents('wpf_status.txt', print_r($data, true));

$db = new DB();
$wfp = new Wfp(WFP);

$response = $wfp->genResponse($data['orderReference']);

if($data['transactionStatus'] != 'Approved'){
    echo json_encode($response);
    exit;
}

$order = $db->getWfpOrderById($data['orderReference']);

if($order['status'] == 'Approved'){exit;}

$oldDate = $db->getPayedDate($order['user_id']);

$obj = [
    'order_id' => $order['id'],
    'user_id' => $order['user_id'],
    'amount' => $data['amount'],
    'date' => $oldDate,
    'status' => $data['transactionStatus'],
];

$result = $db->addPayedPeriod($obj);

if($result){echo json_encode($response);}
