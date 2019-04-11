<?php
include(GetLangFileName(dirname(__FILE__)."/", "/pay.php"));

CModule::IncludeModule('sale');
CModule::IncludeModule('catalog');

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/update_class.php');

session_start();

require_once("revopayment.php");

$revo = new RevoPayment();

$arCurOrderProps = array();

$order_number = $_REQUEST['ORDER_ID'];

$db_res = CSaleOrderPropsValue::GetList(($b=""), ($o=""), array("ORDER_ID"=>$order_number));
while ($ar_res = $db_res->Fetch())
    $arCurOrderProps[(strlen($ar_res["CODE"])>0) ? $ar_res["CODE"] : $ar_res["ID"]] = $ar_res["VALUE"];

$arOrder = CSaleOrder::GetByID($order_number);

$delivery_name = "Без доставки";
$deliveries = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
foreach ($deliveries AS $id => $delivery) {
    if($delivery['CODE'] == $arOrder['DELIVERY_ID']) {
        $delivery_name = $delivery['NAME'];
        break;
    }
}

if(strpos($arOrder['PRICE'], '.') === false) {
    $arOrder['PRICE'] .= ".00";
} elseif(strpos($arOrder['PRICE'], '.') == (strlen($arOrder['PRICE']) - 2)) {
    $arOrder['PRICE'] .= "0";
}

$test_link = ($revo->isTest()) ? "test." : "";

$order_request = array(
    "callback_url"  => "http://" . $test_link . SITE_SERVER_NAME . REVO0_API_CALLBACK_PAGE,
    "redirect_url"  => "http://" . $test_link . SITE_SERVER_NAME . str_replace("%1", $order_number,REVO0_API_RETURN_PAGE),
    "primary_phone" => $arCurOrderProps['PHONE'],
    "primary_email" => $arCurOrderProps['EMAIL'],

    "current_order" => array(
        "order_id"          =>  $order_number,
        "amount"            =>  $arOrder['PRICE'],
    ),

    "person" => array(
        "first_name"    => (isset($fio[1]) ? $fio[1] : ""),
        "surname"       => (isset($fio[0]) ? $fio[0] : ""),
        "patronymic"    => (isset($fio[2]) ? $fio[2] : ""),
        "birth_date"    => ""
    ),

    "skip_result_page"  => false,
);

$order_request = $revo->sendRequest("checkout", $order_request);

if(isset($order_request['status'])) {
    if($order_request['status'] == 0) {
        ?>
        <div class="sale-paysystem-button-container" style="padding:10px 0">
            <a href="<?=$order_request['iframe_url'];?>">
                <button class="btn btn-default btn-buy btn-md">
                    <?= GetMessage('REVO0_PAY_BUTTON_TEXT') ?>
                </button>
            </a>
        </div>
        <?
    } else {
        $error_message = (isset($revo->ERRORS[$order_request['status']]))
            ? $revo->ERRORS[$order_request['status']]
            : $order_request['message'];

        $error_text = $order_request['status'] . " - " . $error_message;
        ?>
        <h4 class="alert-danger">
            Ошибка при оплате:<br><br>
            <?=$error_text?>.
        </h4>
        <?
    }
} else {
    ?>
    <span>Неизвестная ошибка при получении формы оплаты. Попробуйте позднее.</span>
    <?
}