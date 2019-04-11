<?
define('STOP_STATISTICS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$GLOBALS['APPLICATION']->RestartBuffer();

if (!CModule::IncludeModule('sale')) return;

$inputData = file_get_contents('php://input');
// save original data to validate sign
$originalInputData = $inputData;

// TODO REMOVE debug log to file
$req_dump = print_r($_REQUEST, TRUE);
$fp = fopen('request.log', 'a');
fwrite($fp, $req_dump . "\n" . $inputData ."\n\n");
fclose($fp);
// END OF DEBUG LOG

$inputData = json_decode($inputData, true);
// is incoming request contain required fields
if(!isset($inputData['order_id']) || !isset($inputData['amount']) || !isset($inputData['decision'])) die();

// approved payment for order
if($inputData['decision'] == 'approved') {
    $order_number = $inputData['order_id'] . "/1";

    $arOrder = CSaleOrder::GetByID($inputData['order_id']);

    // init paysystem, need for use of props
    $paySystem = new CSalePaySystemAction();
    $paySystem->InitParamArrays($arOrder, $arOrder["ID"]);

    // validate sign
    require_once("revopayment.php");
    $revo = new RevoPayment();
    if(!$revo->validateInputRequest($originalInputData, $_REQUEST['signature'])) die();

    // are price and paid amount equal
    if(floatval($arOrder['PRICE']) == floatval($inputData['amount'])) {
        // set order paid
        CSaleOrder::StatusOrder($order_number, RESULT_ORDER_STATUS);
        CSaleOrder::PayOrder($order_number, "Y", true, true);
    }
}