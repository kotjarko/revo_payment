<?php
class RevoPaymentEventHandlers
{
    function OnSaleOrderEntitySavedHandler ($ID, $val)
    {
        $req_dump = print_r($ID, TRUE);
        $inputData = print_r($val, TRUE);
        $fp = fopen('/home/bitrix/www/test/request.log', 'a');
        fwrite($fp, "PAYMENT" . $req_dump . "\n" . $inputData ."\n\n");
        fclose($fp);
    }

    function OnSalePaymentEntitySavedHandler ($ID, $val)
    {
        $req_dump = print_r($ID, TRUE);
        $inputData = print_r($val, TRUE);
        $fp = fopen('/home/bitrix/www/test/request_pay.log', 'a');
        fwrite($fp, "PAYMENT" . $req_dump . "\n" . $inputData ."\n\n");
        fclose($fp);
    }

    function OnPrintableCheckSendHandler ($ID, $val)
    {
        $req_dump = print_r($ID, TRUE);
        $inputData = print_r($val, TRUE);
        $fp = fopen('/home/bitrix/www/test/request2.log', 'a');
        fwrite($fp, "PAYMENT" . $req_dump . "\n" . $inputData ."\n\n");
        fclose($fp);
    }
}