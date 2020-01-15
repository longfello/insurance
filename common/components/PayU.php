<?php
namespace common\components;

use yii\base\Component;

/**
 * Class PayU Класс обертка платежной системы
 * @package common\components
 */
class PayU extends Component {
    /**
     * LU_URL
     */
    const LU_URL = 'https://secure.payu.ru/order/lu.php';
    /**
     * TOKEN_PAYMENT_URL
     */
    const TOKEN_PAYMENT_URL = 'https://secure.payu.ru/order/tokens/';
    /**
     * IDN_URL
     */
    const IDN_URL = 'https://secure.payu.ru/order/idn.php';
    /**
     * IRN_URL
     */
    const IRN_URL = 'https://secure.payu.ru/order/irn.php';
    /**
     * PAYOUT_LINK_CARD_URL
     */
    const PAYOUT_LINK_CARD_URL = 'https://secure.payu.ru/order/pwa/service.php/UTF/NewPayoutCard';
    /**
     * PAYOUT_URL
     */
    const PAYOUT_URL = 'https://secure.payu.ru/order/prepaid/NewCardPayout';
    /**
     * IOS_URL
     */
    const IOS_URL = 'https://secure.payu.ru/order/ios.php';

    /**
     * Идентификатор мерчанта.
     *
     * @var string
     */
    public $merchantId;
    /**
     * Имя мерчанта.
     *
     * @var string
     */
    public $merchantName;
    /**
     * Секретный ключ.
     *
     * @var string
     */
    public $secretKey;
    /**
     * Режим отладки платежа.
     *
     * @var bool
     */
    public $debug = true;
    /**
     * @param string $merchantId
     * @param string $merchantName
     * @param string $secretKey
     * @param bool $debug
     */
	/*
		function __construct($merchantId, $merchantName, $secretKey, $debug = false)
		{
			$this->merchantId = $merchantId;
			$this->merchantName = $merchantName;
			$this->secretKey = $secretKey;
			$this->debug = $debug;
		}
*/
		/**
		 * Генерация данных для формы оплаты.
		 *
		 * @param array $data данные платежа
		 * @param string $backref URL на который вернется пользователь после оплаты
		 * @param string $tokenType если платеж используется для привязки карты, то указываем тип токена (PAY_ON_TIME или PAY_BY_CLICK)
		 * @return array данные формы
		 */
    function initLiveUpdateFormData(array $data, $backref, $tokenType = null)
    {
        $data['MERCHANT'] = $this->merchantName;
        $data['ORDER_MPLACE_MERCHANT[]'] = $this->merchantId;
        $data['DEBUG'] = $this->debug ? 'TRUE' : 'FALSE';
        $data['BACK_REF'] = $backref;

        if ($tokenType) {
            $data['LU_ENABLE_TOKEN'] = '1';
            $data['LU_TOKEN_TYPE'] = $tokenType;
        }

        $data['ORDER_HASH'] = $this->hashLiveUpdateFormData($data);

        return $data;

    }

    /**
     * Платеж с помощью токена.
     *
     * @param array $data данные платежа
     * @param string $token токен привязанной карты
     * @return array результат запроса
     */
    function createTokenPayment(array $data, $token)
    {
        $data['MERCHANT'] = $this->merchantName;
        $data['REF_NO'] = $token;
        $data['METHOD'] = 'TOKEN_NEWSALE';

        $data['SIGN'] = $this->hashTokenPayment($data);

        $result = $this->sendPostRequest(self::TOKEN_PAYMENT_URL, $data);
        $result = json_decode($result, true);

        return $result;
    }

    /**
     * Выполнение IDN запроса.
     *
     * @param array $data данные IDN запроса
     * @return string результат запроса
     */
    function sendIdnRequest(array $data)
    {
	$uns_data = array();
        $uns_data['MERCHANT'] = $this->merchantName;

	$uns_data = array_merge($uns_data, $data);

        $uns_data['ORDER_HASH'] = $this->hashTokenPayment($uns_data, false);

        $result = $this->sendPostRequest(self::IDN_URL, $uns_data);

        return $result;
    }

    /**
     * Выполнение IRN запроса.
     *
     * @param array $data данные IRN запроса
     * @return string результат запроса
     */
    function sendIrnRequest(array $data)
    {
	$uns_data = array();
        $uns_data['MERCHANT'] = $this->merchantName;

	$uns_data = array_merge($uns_data, $data);

        $uns_data['ORDER_HASH'] = $this->hashTokenPayment($uns_data, false);

        $result = $this->sendPostRequest(self::IRN_URL, $uns_data);

        return $result;
    }

    /**
     * Генерация данных для формы привязки карты (вывод средств).
     *
     * @param array $data данные запроса
     * @param string $backUrl URL на который вернется пользователь после привязки карты
     * @return array данные формы
     */
    function initPayoutLinkCardFormData($data, $backUrl)
    {
        $data['MerchID'] = $this->merchantId;
        $data['BackURL'] = $backUrl;

        $data['Signature'] = $this->hashPayoutData($data);

        return $data;
    }

    /**
     * Запрос вывода средств.
     *
     * @param array $data данные платежа
     * @param string $token токен привязанной карты
     * @return array результат запроса
     */
    function sendPayoutRequest(array $data, $token)
    {
        $data['merchantCode'] = $this->merchantId;
        $data['payin'] = '1';
        $data['token'] = $token;

        $data['signature'] = $this->hashPayoutData($data);

        $result = $this->sendPostRequest(self::PAYOUT_URL, $data);
        $result = json_decode($result, true);

        return $result;
    }

    /**
     * Обработка IPN запроса.
     *
     * @return string строка ответа на IPN запрос
     */
    function handleIpnRequest()
    {
        $ipnPid = isset($_POST['IPN_PID']) ? $_POST['IPN_PID'] : '';
        $ipnName = isset($_POST['IPN_PNAME']) ? $_POST['IPN_PNAME'] : '';
        $ipnDate = isset($_POST['IPN_DATE']) ? $_POST['IPN_DATE'] : '';

        $date = date('YmdHis');
        $hash =
            strlen($ipnPid[0]) . $ipnPid[0] .
            strlen($ipnName[0]) . $ipnName[0] .
            strlen($ipnDate) . $ipnDate .
            strlen($date) . $date;
        $hash = hash_hmac('md5', $hash, $this->secretKey);

        $result = '<EPAYMENT>' . $date . '|' . $hash . '</EPAYMENT>';

        return $result;
    }

    /**
     * Отправка IOS запроса
     * @param integer $refNo
     * @return string
     */
    function sendIosRequest($refNo)
    {
        $data = array(
            'MERCHANT' => $this->merchantName,
            'REFNOEXT' => $refNo,
        );
        $data['HASH'] = $this->hashLiveUpdateFormData($data);

        $result = $this->sendPostRequest(self::IOS_URL, $data);

        return $result;
    }

    /**
     * Генерация контрольной суммы для LU запроса.
     *
     * @param array $data
     * @return string
     */
    protected function hashLiveUpdateFormData(array $data)
    {
        $hash  = $this->hash('MERCHANT', $data);
        $hash .= $this->hash('ORDER_REF', $data);

	    $hash .= $this->hash('DESTINATION_CITY	', $data);
	    $hash .= $this->hash('DESTINATION_COUNTRY', $data);
	    $hash .= $this->hash('DESTINATION_STATE', $data);
        $hash .= $this->hash('ORDER_DATE', $data);
	    $hash .= $this->hash('ORDER_PNAME[]', $data);
	    $hash .= $this->hash('ORDER_PCODE[]', $data);
	    $hash .= $this->hash('ORDER_PINFO[]', $data);
        $hash .= $this->hash('ORDER_PRICE[]', $data);
	    $hash .= $this->hash('ORDER_PRICE_TYPE[]', $data);
        $hash .= $this->hash('ORDER_QTY[]', $data);
        $hash .= $this->hash('ORDER_SHIPPING', $data);
	    $hash .= $this->hash('ORDER_VAT[]', $data);
	    $hash .= $this->hash('PAY_METHOD', $data);
        $hash .= $this->hash('PRICES_CURRENCY', $data);
        $hash .= $this->hash('ORDER_MPLACE_MERCHANT[]', $data);
	    if (isset($data['TESTORDER']) && $data['TESTORDER'] == 'TRUE'){
		    $hash .= $this->hash('TESTORDER', $data);
	    }

	    echo $hash;
	    /*
			  echo $hash;
8ynynynhj3117192017-05-12 12:12:194#11722Strahovaa kompania ERV16Insurance police34.511103EUR4TRUE8ynynynhj
8ynynynhj3117192017-05-12 12:12:1916Insurance police4#11722Strahovaa kompania ERV34.511103EUR8ynynynhj4TRUE

<form action="https://secure.payu.ru/order/lu.php" method="post">
	<input type="hidden" name="ORDER_DATE" value="2017-05-12 12:12:19">
	<input type="hidden" name="ORDER_PNAME[]" value="Insurance police">
	<input type="hidden" name="ORDER_PCODE[]" value="#117">
	<input type="hidden" name="ORDER_PINFO[]" value="Strahovaa kompania ERV">
	<input type="hidden" name="ORDER_PRICE[]" value="4.5">
	<input type="hidden" name="ORDER_QTY[]" value="1">
	<input type="hidden" name="ORDER_VAT[]" value="0">
	<input type="hidden" name="ORDER_REF" value="117">
	<input type="hidden" name="PRICES_CURRENCY" value="EUR">
	<input type="hidden" name="LANGUAGE" value="RU">
	<input type="hidden" name="TESTORDER" value="TRUE">
	<input type="hidden" name="AUTOMODE" value="1">
	<input type="hidden" name="BILL_FNAME" value="asdasd">
	<input type="hidden" name="BILL_LNAME" value="asdasdas">
	<input type="hidden" name="BILL_EMAIL" value="sdasd@asdasd.asd">
	<input type="hidden" name="BILL_PHONE" value="+74954118444">
	<input type="hidden" name="DELIVERY_FNAME" value="asdasd">
	<input type="hidden" name="DELIVERY_LNAME" value="asdasdas">
	<input type="hidden" name="DELIVERY_PHONE" value="+74954118444">
	<input type="hidden" name="MERCHANT" value="ynynynhj">
	<input type="hidden" name="ORDER_MPLACE_MERCHANT[]" value="ynynynhj">
	<input type="hidden" name="DEBUG" value="TRUE">
	<input type="hidden" name="BACK_REF" value="http://bullosafe.kvk-dev.pp.ua/calc-payment-done.html?order=117">
	<input type="hidden" name="LU_ENABLE_TOKEN" value="1">
	<input type="hidden" name="LU_TOKEN_TYPE" value="PAY_BY_CLICK">
	<input type="hidden" name="ORDER_HASH" value="fa42084ab59c71e3395ad19854abb76f">
</form>
(METCHANT)                  8   ynynynhj
(ORDER_REF)                 3   117
(ORDER_DATE)                19  2017-05-12 12:12:19
(ORDER_PCODE[])             4   #117
(ORDER_PNAME[])             22  Strahovaa kompania ERV
(ORDER_PINFO[])             16  Insurance police
(ORDER_PRICE[])             3   4.5
(ORDER_QTY[])               1   1
(ORDER_VAT[])               1   0
(PRICES_CURRENCY)           3   EUR
(TESTORDER)                 4   TRUE
(ORDER_MPLACE_MERCHANT[])   8   ynynynhj



	    				*/
        return hash_hmac('md5', $hash, $this->secretKey);
    }

    /**
     * Проверка подписи
     * @return bool
     */
    public function checkResultUrl(){
    	$url = \Yii::$app->request->absoluteUrl;

	    $source = preg_replace('/&ctrl=(.*)/', '', $url);
	    $source = strlen($source).$source;
	    $hash   = hash_hmac('md5', $source, $this->secretKey);;

	    return $hash == \Yii::$app->request->get('ctrl');
    }

    /**
     * Рекурсивная для массивов
     */
    function hash($field, $data) {
    	if (isset($data[$field])){
		    return strlen($data[$field]) . $data[$field];
	    } else return '';
    }

    /**
     * Хешер
     *
     * @param $data
     *
     * @return string
     */
    function hasher($data) {
        $ignoredKeys = array(
            'ORDER_REF',
            'AUTOMODE',
            'BACK_REF',
            'DEBUG',
            'BILL_FNAME',
            'BILL_LNAME',
            'BILL_EMAIL',
            'BILL_PHONE',
            'BILL_ADDRESS',
            'BILL_CITY',
            'DELIVERY_FNAME',
            'DELIVERY_LNAME',
            'DELIVERY_PHONE',
            'DELIVERY_ADDRESS',
            'DELIVERY_CITY',
            'LU_ENABLE_TOKEN',
            'LU_TOKEN_TYPE',
//            'TESTORDER',
            'LANGUAGE',
            'ORDER_MPLACE_MERCHANT[]',
            'MERCHANT'
        );

        $hash = '';
        foreach ($data as $dataKey => $dataValue) {
	    if (is_array($dataValue)) 
                $hash .= $this->hasher($dataValue);
	    else {
                if (!in_array($dataKey, $ignoredKeys, true))
			$hash .= /*'('.$dataKey.')'.*/strlen($dataValue) . $dataValue;
	    }
        }
        return $hash;
    }

    /**
     * Генерация контрольной суммы для платежа с помощью токена.
     *
     * @param array $data
     * @return string
     */
    protected function hashTokenPayment(array $data, $sort = true)
    {
        if ($sort) ksort($data);

        $hash = '';
        foreach ($data as $dataValue) {
            $hash .= strlen($dataValue) . $dataValue;
        }
        $hash = hash_hmac('md5', $hash, $this->secretKey);

        return $hash;
    }

    /**
     * Генерация контрольной суммы для запросов типа Payout
     *
     * @param array $data
     * @return string
     */
    protected function hashPayoutData(array $data)
    {
        ksort($data);

        $hash = implode($data) . $this->secretKey;
        $hash = md5($hash);

        return $hash;
    }

    /**
     * Отправка POST-запроса
     * @param string $url
     * @param array $data
     * @return string
     */
    protected function sendPostRequest($url, array $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
	if ($result === false) echo 'CURL error: ' . curl_error($ch);
        curl_close($ch);

        return $result;
    }
}
