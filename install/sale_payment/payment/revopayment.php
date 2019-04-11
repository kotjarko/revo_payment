<?php
/**
 * описание API
 * https://revotechnology.github.io/api-factoring/
 *
 * основано на
 * https://github.com/RevoTechnology/revo-sdk-php/
 *
 */

use \Bitrix\Main\Web\HttpClient;

require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/revo.payment/config.php");

class RevoPayment
{
    /**
     * @var array Кастомизация сообщений об ошибках, заменяет стандартный текст ошибки от Revo
     */
    public $ERRORS = [
        "22" => "Заказ уже оплачен ранее",
        "23" => "Срок оплаты заказа истёк",
        "33" => "Стоимость заказа не доступна к оплате с помощью сервиса",
    ];

    /**
     * @var array Ссылки на страницы API
     */
    private $ENDPOINTS = [
        'reg'       => '/factoring/v1/limit/auth',
        'checkout'  => '/factoring/v1/precheck/auth'
    ];

    /**
     * Включен ли в данный момент режим использования тестовых данных
     *
     * @return bool
     */
    public function isTest()
    {
        return (CSalePaySystemAction::GetParamValue("USE_TEST") == 'Y');
    }

    /**
     * Расчёт подписи
     *
     * @param $request  - тело запроса
     *
     * @return string   - подпись
     */
    public function calculateSignature($request)
    {
        $secret = ($this->isTest())
            ? CSalePaySystemAction::GetParamValue("TEST_SECRET_KEY")
            : CSalePaySystemAction::GetParamValue("SECRET_KEY");

        return sha1($request . $secret);
    }

    /**
     * Проверка подписи запроса
     *
     * @param string $data      - тело запроса
     * @param string $signature - подпись
     *
     * @return bool
     */
    public function validateInputRequest($data, $signature)
    {
          return ($this->calculateSignature($data) == $signature);
    }

    /**
     * Сборка URL для запроса к API
     *
     * @param string $service_type - тип запроса (страница)
     * @param array $query         - массив параметров
     *
     * @return string              - полная ссылка с хостом, путём и параметрами
     */
    private function buildUrl($service_type, $query)
    {
        $host = ($this->isTest())
            ? REVO0_API_TEST_URL
            : REVO0_API_URL;

        return $host . $this->ENDPOINTS[$service_type] . '?' . http_build_query($query);
    }

    /**
     * Отправка запроса к API
     *
     * @param string $service_type - тип запроса (страница)
     * @param array  $data         - тело (body) запроса
     * @param array  $query        - массив параметров
     *
     * @return array               - декодированный JSON ответа
     */
    public function sendRequest($service_type, $data, $query = array())
    {
        $data = json_encode($data);

        $query['store_id'] = ($this->isTest())
            ? CSalePaySystemAction::GetParamValue("TEST_STORE_ID")
            : CSalePaySystemAction::GetParamValue("STORE_ID");

        $query['signature'] = $this->calculateSignature($data);
        
        $url = $this->buildUrl($service_type, $query);

        $http = new \Bitrix\Main\Web\HttpClient;
        $result = $http->post($url, $data);

        return json_decode($result, true);
    }
}