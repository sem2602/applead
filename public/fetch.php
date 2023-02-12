<?php

use App\Model\DB;
use App\Services\Prom;

require_once '../vendor/autoload.php';

$db = new DB();

if($_REQUEST['method'] === 'save_settings'){
    
    $db->setUserSettings($_REQUEST);
    
    echo json_encode(['data' => true]);

}

if($_REQUEST['method'] === 'update_settings'){

    $db->updateSettings($_REQUEST);

    echo json_encode(['data' => $_REQUEST]);

}

if($_REQUEST['method'] === 'create_settings'){

    $db->setSettings($_REQUEST['user_id'], $_REQUEST);

    echo json_encode(['data' => true]);

}

if($_REQUEST['method'] === 'delete_settings'){

    $db->deleteSettings($_REQUEST['id'],);

    echo json_encode(['data' => true]);

}

if($_REQUEST['method'] === 'test_api'){

    $prom = new Prom($_REQUEST['key']);

    $data = $prom->getProductList();

    echo json_encode(['data' => $data]);

}
