<?php
/**
 * Created by PhpStorm.
 * User: ivancov
 * Date: 14.01.2019
 * Time: 11:46
 */

namespace Sale\Handlers\PaySystem;


class RevoPaymentHandler extends PaySystem\BaseServiceHandler
{
    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {
        $params = array(
            'PARAM1' => 'VALUE1',
            'PARAM2' => 'VALUE2',
        );
        $this->setExtraParams($params);
        return $this->showTemplate($payment, "template");
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        $paymentId = $request->get('ORDER');
        $paymentId = preg_replace("/^[0]+/","",$paymentId);
        return intval($paymentId);
    }

    /**
     * @return array
     */
    public function getCurrencyList()
    {
        return array('RUB');
    }

    /**
     * @return array
     */
    public static function getIndicativeFields()
    {
        return array('PARAM1','PARAM2');
    }

    /**
     * @param Request $request
     * @param $paySystemId
     * @return bool
     */
    static protected function isMyResponseExtended(Request $request, $paySystemId)
    {
        return true;
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @return PaySystem\ServiceResult
     */
    public function processRequest(Payment $payment, Request $request)
    {
        $result = new PaySystem\ServiceResult();
        $action = $request->get('ACTION');
        $data = $this->extractDataFromRequest($request);

        $data['CODE'] = $action;

        if($action==="1")
        {
            $result->addError(new Error("Ошибка платежа"));
        }
        elseif($action==="0")
        {
            $fields = array(
                "PS_STATUS_CODE" => $action,
                "PS_STATUS_MESSAGE" => '',
                "PS_SUM" => $request->get('AMOUNT'),
                "PS_CURRENCY" => $payment->getField('CURRENCY'),
                "PS_RESPONSE_DATE" => new DateTime(),
                "PS_INVOICE_ID" => '',
            );
            if ($this->isCorrectSum($payment, $request))
            {
                $data['CODE'] = 0;
                $fields["PS_STATUS"] = "Y";
                $fields['PS_STATUS_DESCRIPTION'] = "Оплата произведена успешно";
                $result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
            }
            else
            {
                $data['CODE'] = 200;
                $fields["PS_STATUS"] = "N";
                $message = "Неверная сумма платежа";
                $fields['PS_STATUS_DESCRIPTION'] = $message;
                $result->addError(new Error($message));
            }
            $result->setPsData($fields);
        }
        else
        {
            $result->addError(new Error("Неверный статус платежной системы при возврате информации о платеже"));
        }

        $result->setData($data);

        if (!$result->isSuccess())
        {
            PaySystem\ErrorLog::add(array(
                'ACTION' => "processRequest",
                'MESSAGE' => join('\n', $result->getErrorMessages())
            ));
        }

        return $result;
    }
}