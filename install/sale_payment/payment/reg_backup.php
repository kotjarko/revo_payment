<?php

$fio = explode(" ", $arCurOrderProps['FIO']);

$register_request = array(
    "callback_url"  => "http://test." . SITE_SERVER_NAME . RBS0_API_CALLBACK_PAGE,
    "redirect_url"  => "http://test." . SITE_SERVER_NAME . str_replace("%1", $order_number,REVO0_API_RETURN_PAGE),
    "primary_phone" => $arCurOrderProps['PHONE'],
    "primary_email" => $arCurOrderProps['EMAIL'],
    "current_order" => array(
        "order_id"      =>  $order_number
    ),
    "person" => array(
        "first_name"    => (isset($fio[1]) ? $fio[1] : ""),
        "surname"       => (isset($fio[0]) ? $fio[0] : ""),
        "patronymic"    => (isset($fio[2]) ? $fio[2] : ""),
        "birth_date"    => ""
    ),
);

$reg_form_request = $revo->sendRequest("reg", $register_request);

if(isset($reg_form_request['status'])) {
    if($reg_form_request['status'] == 0) {
        ?>
        <div class="sale-paysystem-button-container" style="padding:10px 0">
            <a href="<?=$reg_form_request['iframe_url'];?>">
                <button class="btn btn-default btn-buy btn-md">
                    <?= GetMessage('REVO0_PAY_BUTTON_TEXT') ?>
                </button>
            </a>
        </div>
        <?
    } else {
        $error_text = $reg_form_request['status'] . " - " . $reg_form_request['message'];
        ?>
        <span>Ошибка при оплате: <?=$error_text?>.</span>
        <?
    }
} else {
    ?>
    <span>Неизвестная ошибка при получении формы оплаты.</span>
    <?
}

echo "<pre>";
var_dump($register_request);
echo "</pre>";