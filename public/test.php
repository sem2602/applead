<?php

use App\Model\DB;
use App\Services\Wfp;

require_once '../vendor/autoload.php';
require_once '../config.php';

$db = new DB();

$order_id = $db->addWfpOrder(230);

echo '<pre>';
var_dump($order_id);
exit;





$wfp = new Wfp(WFP);

$inputData = $wfp->getForm(999999, 450);

?>

<form method="post" action="https://secure.wayforpay.com/pay" accept-charset="utf-8">
    
    <?=$inputData?>
    
    <button type="submit">450</button>
    
</form>
