<?php

namespace common\modules\ApiRgs;

use Yii;
use yii\helpers\FileHelper;
use yii\httpclient\Client;
use yii\web\Cookie;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\Exception;

use common\components\ApiModule;
use common\components\Calculator\forms\TravelForm;
use common\models\Orders;
use common\models\Currency as DictCurrency;

use common\modules\ApiRgs\components\ProgramSearch;
use common\modules\ApiRgs\models\Product;
use common\modules\ApiRgs\models\Classifier;
use common\modules\ApiRgs\models\Currency;
use common\modules\ApiRgs\models\TerritoryType;
use common\modules\ApiRgs\models\Country;
use common\modules\ApiRgs\models\Country2dict;

class Module extends ApiModule {

    /**
     * Формат данных
     */
    const DATA_FORMAT = 'json';

    /**
     * Кука авторизации
     */
    const AUTH_COOKIE = '.INSURANCE';

    /**
     * Имя http заголовка для отправки параметров
     */
    const PARAMS_HEADER = 'x-vs-parameters';

    /*
     * Сервис авторизации
     */
    const AUTH_SERVICE = 'Authentication_JSON_AppService.axd/Login';

    /*
     * Сервис получения справочников
     */
    const CLASSIFIER_SERVICE = 'ClassifierFeature/Classifier.dat';

    /*
     * Сервис калькуляции
     */
    const CALCULATE_SERVICE = 'RGSTravelFeature/Calculate.cmd';

    /*
     * Сервис сохранения полиса
     */
    const SAVE_SERVICE = 'RGSTravelFeature/UpdatePolicy.cmd';

    /*
     * Сервис акцептации полиса
     */
    const ACCEPT_SERVICE = 'RGSTravelFeature/AcceptPolicy.cmd';

    /*
     * Сервис печати полиса
     */
    const PDF_SERVICE = 'RGSTravelFeature/GetPrintForm.cmd';

    /**
     * @var array окружения
     */
    public $environments = [
        self::ENV_TEST => [
            'uri' => 'https://preprodrgs.virtusystems.ru/',
            'userName' => 'BULLO',
            'password' => 'jnB2HvXq'
        ],
        self::ENV_PROD => []
    ];

    /**
     * @inheritdoc
     */
    public $uri;

    /**
     * @var string Логин
     */
    public $userName;

    /**
     * @var string Пароль
     */
    public $password;

    /**
     * @var string Кука авторизации
     */
    public $authCookie = null;

    /**
     * @var Product Продукт
     */
    public $product = null;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        foreach ($this->environments[getenv('RGS_MODE')] as $k => $v) {
            $this->$k = $v;
        }

        $this->product = $this->getProduct();

        $this->loadAuthCookie();
    }

    /**
     * Получение продукта страхования (ВЗР_174_bullosafe.ru_сайт партнера)
     *
     * @return Product
     */
    public function getProduct() {
        return Product::findOne(1);
    }

    /**
     * Создание объекта запроса к API.
     * 
     * @param string $service
     * @param string $method
     * @param null|array $data
     * 
     * @return yii\httpclient\Request
     */
    private function getRequest($service, $method = 'get', $data = null) {
        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport'
        ]);
        $request = $client->createRequest()
            ->setMethod($method)
            ->setFormat(self::DATA_FORMAT)
            ->setOptions([
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false
            ])
            ->setUrl($this->uri . $service);

        if (!is_null($data)) {
            if ($method == 'get') {
                $request->addHeaders([
                    self::PARAMS_HEADER => json_encode($data)
                ]);
            } else {
                $request->setData($data);
            }
        }

        return $request;
    }

    /**
     * Авторизация API
     * 
     * @return true
     * @throws Exception
     */
    private function apiLogin() {
        $response = $this->getRequest(self::AUTH_SERVICE, 'post', [
            'userName' => $this->userName,
            'password' => $this->password,
            'createPersistentCookie' => 'true'
        ])->send();

        // Check http code
        $http_code = $response->getHeaders()->get('http-code');
        if ($http_code !== "200") {
            throw new Exception('Request failed with status code ' . $http_code, $http_code);
        }

        // Check result
        $result = $response->getData();
        if ($result['d'] !== true) {
            throw new Exception('Invalid username or password', 400);
        }

        // Check cookie
        $this->authCookie = $response->getCookies()->getValue(self::AUTH_COOKIE);
        if (is_null($this->authCookie)) {
            throw new Exception('Internal Server Error', 500);
        }
        $this->saveAuthCookie();

        return true;
    }

    /**
     * Запрос API
     * 
     * @param string $service
     * @param array $data
     * @param string $method
     * @param boolean $forceLogin
     * 
     * @return Array|null
     * @throws Exception
     */
    public function apiRequest($service, $data = [], $method = 'get', $forceLogin = false) {
        if (is_null($this->authCookie) || $forceLogin) {
            $this->apiLogin();
        }

        $cookie = new Cookie();
        $cookie->name = self::AUTH_COOKIE;
        $cookie->value = $this->authCookie;

        $response = $this->getRequest($service, $method, $data)->setCookies([$cookie])->send();

        // Check http code
        $http_code = $response->getHeaders()->get('http-code', null, false);
        if (!is_array($http_code) || !in_array("200", $http_code)) {
            if ($forceLogin) {
                throw new Exception('Request failed with status code ' . $http_code[0], $http_code[0]);
            }

            return $this->apiRequest($service, $data, $method, true);
        }

        $responseData = $response->getData()['d'];

        // Check response
        if (!$responseData['IsValid']) {
            throw new Exception('Api returned error for service ' . $service . ':' . PHP_EOL . print_r($responseData['Errors'], true), '500');
        }

        return $responseData['Result'];
    }

    /**
     * Сохранение куки авторизации в файл
     * 
     * @return void
     */
    private function saveAuthCookie() {
        $filename = FileHelper::normalizePath(Yii::getAlias('@tmp/' . self::className() . self::AUTH_COOKIE));
        FileHelper::createDirectory(dirname($filename));

        $fp = fopen($filename, 'w');
        fwrite($fp, $this->authCookie);
        fclose($fp);
    }

    /**
     * Установка куки авторизации из файла
     * 
     * @return string|false
     */
    private function loadAuthCookie() {
        $filename = FileHelper::normalizePath(Yii::getAlias('@tmp/' . self::className() . self::AUTH_COOKIE));

        if (file_exists($filename)) {
            $fp = fopen($filename, "r");
            $data = fread($fp, filesize($filename));
            fclose($fp);

            if (!empty($data)) {
                $this->authCookie = $data;

                return $this->authCookie;
            }
        }

        return false;
    }

    /**
     * Получение справочника по имени класса
     * 
     * @param string $class
     * 
     * @return ActiveRecord|null
     */
    public function findClassifier($class) {
        return Classifier::findOne(['class' => '\common\modules\ApiRgs\models\\' . $class]);
    }

    /**
     * Получение данных справочника через апи
     * 
     * @param string $id
     * 
     * @return Array|null
     */
    public function loadClassifierFromApi($id) {
        return $this->apiRequest(self::CLASSIFIER_SERVICE, [
            'id' => $id,
            'productId' => $this->product->ext_id
        ]);
    }

    /**
     * Получение списка валют через апи
     * 
     * @return Array|null
     */
    public function getCurrenciesFromApi() {
        $classifier = $this->findClassifier('Currency');

        if ($classifier !== null) {
            return $this->loadClassifierFromApi($classifier->ext_id);
        }

        return null;
    }

    /**
     * Получение списка территорий через апи
     * 
     * @return Array|null
     */
    public function getTerritoriesFromApi() {
        $classifier = $this->findClassifier('TerritoryType');

        if ($classifier !== null) {
            return $this->loadClassifierFromApi($classifier->ext_id);
        }

        return null;
    }

    /**
     * Получение списка стран через апи
     * 
     * @return Array|null
     */
    public function getCountriesFromApi() {
        $classifier = $this->findClassifier('Country');

        if ($classifier !== null) {
            return $this->loadClassifierFromApi($classifier->ext_id);
        }

        return null;
    }

    /**
     * Получение списка видов рисков через апи
     * 
     * @return Array|null
     */
    public function getRiskTypesFromApi() {
        $classifier = $this->findClassifier('RiskType');

        if ($classifier !== null) {
            return $this->loadClassifierFromApi($classifier->ext_id);
        }

        return null;
    }

    /**
     * Получение списка программ страхования через апи
     * 
     * @return Array|null
     */
    public function getProgramsFromApi() {
        $classifier = $this->findClassifier('Program');

        if ($classifier !== null) {
            return $this->loadClassifierFromApi($classifier->ext_id);
        }

        return null;
    }

    /**
     * Получение списка страховых сумм через апи
     * 
     * @return Array|null
     */
    public function getSumsFromApi() {
        $classifier = $this->findClassifier('Sum');

        if ($classifier !== null) {
            return $this->loadClassifierFromApi($classifier->ext_id);
        }

        return null;
    }

    /**
     * Получение списка видов дополнительных условий через апи
     * 
     * @return Array|null
     */
    public function getAdditionalConditionTypesFromApi() {
        $classifier = $this->findClassifier('AdditionalConditionType');

        if ($classifier !== null) {
            return $this->loadClassifierFromApi($classifier->ext_id);
        }

        return null;
    }

    /**
     * Получение списка дополнительных условий через апи
     * 
     * @return Array|null
     */
    public function getAdditionalConditionsFromApi() {
        $classifier = $this->findClassifier('AdditionalCondition');

        if ($classifier !== null) {
            return $this->loadClassifierFromApi($classifier->ext_id);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function search(TravelForm $form) {
        $searcher = new ProgramSearch([
            'form' => $form,
            'module' => $this
        ]);

        return $searcher->findAll();
    }

    /**
     * @inheritdoc
     * 
     * @param array $programs
     *
     * @return array
     */
    function getProgram($programs) {
        return $programs;
    }

    /**
     * @inheritdoc
     */
    function getProgramId($sum) {
        return $sum->id;
    }

    /**
     * @inheritdoc
     * 
     * @param array $programs
     * @param TravelForm $form
     * @param string $calc_type
     *
     * @return float|int|Orders
     * @throws Exception
     */
    public function calcPrice($programs, $form, $calc_type = self::CALC_LOCAL) {
        switch ($calc_type) {
            case self::CALC_LOCAL:
                return $this->getPrice($form, $programs);
            break;
            case self::CALC_API:
                //
            break;
            default:
                throw new Exception('Calculation type not implemented: ' . $calc_type, 501);
            break;
        }
    }

    /**
     * Получение стоимости из АПИ
     * 
     * @param TravelForm $form
     * @param array $programs
     *
     * @return string|int 0
     */
    public function getPrice(TravelForm $form, $programs) {
        $data = ['policy' => $this->getData($form, $programs)];

        try {
            $result = $this->apiRequest(self::CALCULATE_SERVICE, $data, 'post');

            if (!empty($result) && isset($result['RurPremium'])) {
                return number_format((float)$result['RurPremium'], 2, '.', '');
            } else {
                return 0;
            }
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * @inheritdoc
     * 
     * @param TravelForm $form
     * @param array $programs
     *
     * @return false|Orders
     * @throws Exception
     */
    public function getOrder(TravelForm $form, $programs) {
        /** @var array $programs */
//        $programs = $this->getProgram($programs);

        /** @var \common\models\Person $payer_model */
        $payer_model = $this->getHolder($form->payer);

        $data = ['data' => $this->getData($form, $programs)];

        try {
            $result = $this->apiRequest(self::SAVE_SERVICE, $data, 'post');
            if (!empty($result) && isset($result['RurPremium'])) {
                $order = new Orders();
                $order->api_id = $this->model->id;
                $order->price = number_format((float)$result['RurPremium'], 2, '.', '');
                $order->currency_id = DictCurrency::findOne(['char_code' => DictCurrency::RUR])->id;
                $order->status = Orders::STATUS_NEW;
                $order->holder_id = $payer_model->id;
                $order->info = [
                    'request' => $data,
                    'responce' => $result
                ];
                $order->calc_form = $form;
                $order->program = $programs;
                if (!$order->save()) {
                    throw new Exception(strip_tags(Html::errorSummary($order)), 500);
                }

                return $order;
            }

            throw new Exception('Error retrieving result: ' . $result, 500);
        } catch (Exception $e) {
            throw new Exception('Error retrieving result: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @inheritdoc
     * 
     * @param Orders $order
     *
     * @throws Exception
     */
    public function confirmApiPayment(Orders $order) {
        $res = false;

        $policy = $order->info['responce'];
        $policy['Profile'] = ['ShowDmsNumberAndUrList' => null];

        //accept
        $result = $this->apiRequest(self::ACCEPT_SERVICE, ['data' => $policy], 'post');

        $order_info = $order->info;
        $order_info['request_pay'] = ['data' => $policy];
        $order_info['responce_pay'] = $result;
        $order->info = $order_info;

        if (!empty($result) && $result['RurPremium']) {
            $res = true;
            $order->status = Orders::STATUS_PAYED_API;
        }

        if (!$order->save()) {
            Yii::error($this->name . " confirmApiPayment error" . print_r($order->getErrors(), true));
        }

        return $res;
    }

    /**
     * @inheritdoc
     * 
     * @param Orders $order
     * @param Array|null $policeObj
     *
     * @return array
     * @throws Exception
     */
    public function downloadOrder(Orders $order, $policeObj = null) {
        $log = [];

        if ($order->status == Orders::STATUS_PAYED_API) {
            if (is_null($policeObj) && isset($order->info['responce_pay'])) {
                $policeObj = $order->info['responce_pay'];
            }

            if (!is_null($policeObj)) {
                $log[time()] = 'Старт загрузки информации из апи';

                // get pdf
                $pdf_bytes = $this->apiRequest(self::PDF_SERVICE, [
                    'policyID' => $policeObj['ID']
                ], 'post');

                $log[time()] = 'Получен ответ: <pre>' . print_r($pdf_bytes, true) . '</pre>';

                if (!empty($pdf_bytes)) {
                    $folder = $this->getOrderFolder($order);
                    $file = $folder . 'police.pdf';

                    $log[time()] = 'Сохранение полиса из base64 в ' . $file;

                    $pdf_bytes_array = str_split($pdf_bytes, 3);
                    $pdf = '';
                    foreach ($pdf_bytes_array as $byte) {
                        $pdf .= pack('C*', $byte);
                    }
                    file_put_contents($file, $pdf);
                } else {
                    $log[time()] = 'Ошибка при получении полиса';
                }

                $log[time() + 1] = 'Завершено';
            }
        }

        return $log;
    }

    /**
     * Формирование объекта полиса
     * 
     * @param TravelForm $form
     * @param array $programs
     *
     * @return array
     * 
     * @throws Exception
     */
    public function getData(TravelForm $form, $programs) {
        try {
            $currency = Currency::getDefault();
            $countries = $this->getCountries($form);
            $risks = $this->getRisks($programs);

            $data = ArrayHelper::merge([
                'ProductID' => $this->product->ext_id,
                'DocumentDate' => date('Y-m-d'),
                'AmountCurrencyCd' => $currency->ext_id,
                'AmountCurrencyCODE' => $currency->title,
                'EffectiveDate' => Yii::$app->formatter->asDate($form->dateFrom, 'php:Y-m-d'),
                'ExpirationDate' => Yii::$app->formatter->asDate($form->dateTo, 'php:Y-m-d'),
                'Duration' => $form->dayCount,
                'Insurer' => $this->getInsurer($form),
                'Insured' => $this->getInsureds($form),
                'Risks' => $risks,
                'ExtraConditions' => $programs['additionalConditions']
            ], $countries);

            if ($form->payer && $form->payer->email) {
                $data['Email'] = $form->payer->email;
            }

            return $data;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Обработка выбранных стран
     * 
     * @param TravelForm $form
     * @param boolean $min_sum Получаем только минимальную страховую сумму
     *
     * @return array
     * 
     * @throws Exception
     */
    public function getCountries(TravelForm $form, $min_sum = false) {
        if (empty($form->countries)) {
            throw new Exception('Страна: Это поле обязательно для заполнения');
        }

        if (count($form->territories)) {
            return $this->getTerritory($form, $min_sum);
        }

        try {
            $countries = null;

            $select = !$min_sum ? [
                'country_id' => 'arc.ext_id',
                'country_title' => 'arc.title',
                'territory_id' => 'artt.ext_id'
            ] : [
                'arc.min_sum'
            ];
            $query = (new Query())->select($select)->from([
                'arc' => Country::tableName()
            ])->leftJoin([
                'artt' => TerritoryType::tableName()
            ], 'arc.territory_type_id = artt.id')->leftJoin([
                'arc2d' => Country2dict::tableName()
            ], 'arc.id = arc2d.country_id')->where([
                'arc2d.internal_id' => $form->countriesOverall,
                'arc.enabled' => 1
            ])->orderBy([
                'arc.territory_type_id' => SORT_DESC
            ]);
            $data = !$min_sum ? $query->all() : $query->max('arc.min_sum');

            if ($min_sum && $data !== false && !is_null($data)) {
                return $data;
            }

            if (!empty($data)) {
                $result = ArrayHelper::index($data, null, 'territory_id');
                foreach ($result as $territory => $countries_arr) {
                    if (count($countries_arr) == count($form->countriesOverall)) {
                        $countries = [
                            'TerritoryType' => $territory,
                            'Countries' => implode(',', ArrayHelper::getColumn($countries_arr, 'country_id')),
                            'CountriesText' => implode(',', ArrayHelper::getColumn($countries_arr, 'country_title'))
                        ];

                        break;
                    }
                }
            }

            if (is_null($countries)) {
                throw new Exception('Не найдены выбранные страны');
            }

            return $countries;
        } catch (Exception $e) {
            return $this->getTerritory($form, $min_sum, true);
        }
    }

    /**
     * Обработка выбранных территорий
     * 
     * @param TravelForm $form
     * @param boolean $min_sum
     * @param boolean $all_world
     *
     * @return array
     * 
     * @throws Exception
     */
    public function getTerritory(TravelForm $form, $min_sum = false, $all_world = false) {
        if ($all_world) {
            $result = Country::getAllWorld($min_sum);
        } else {
            try {
                $select = !$min_sum ? [
                    'Countries' => 'arc.ext_id',
                    'CountriesText' => 'arc.title',
                    'TerritoryType' => 'artt.ext_id'
                ] : [
                    'arc.min_sum'
                ];
                $query = (new Query())->select($select)->from([
                    'arc' => Country::tableName()
                ])->leftJoin([
                    'artt' => TerritoryType::tableName()
                ], 'arc.territory_type_id = artt.id')->leftJoin([
                    'arc2d' => Country2dict::tableName()
                ], 'arc.id = arc2d.country_id')->where([
                    'arc2d.internal_id' => $form->territories,
                    'arc.enabled' => 1
                ])->orderBy([
                    'arc.territory_type_id' => SORT_DESC
                ]);
                $result = !$min_sum ? $query->one() : $query->max('arc.min_sum');

                if ($result === false || is_null($result)) {
                    throw new Exception('Выбранные территории не привязаны к территориям из api');
                }
            } catch (Exception $e) {
                return $this->getTerritory($form, $min_sum, true);
            }
        }

        return $result;
    }

    /**
     * Обработка выбранных рисков
     * 
     * @param array $programs
     * 
     * @return array
     */
    public function getRisks($programs) {
        $risks = [];

        $main_sum = $programs['main'];
        $main_program = $main_sum->programModel;
        $main_risk = $main_program->riskType;
        $risks[] = [
            'RiskID' => $main_risk->ext_id,
            'VariantID' => $main_program->ext_id,
            'SumInsuredID' => $main_sum->ext_id
        ];

        if (!empty($programs['additionalRisks'])) {
            foreach ($programs['additionalRisks'] as $additional_sum) {
                $additional_program = $additional_sum->programModel;
                $additional_risk = $additional_program->riskType;
                $risks[] = [
                    'RiskID' => $additional_risk->ext_id,
                    'VariantID' => $additional_program->ext_id,
                    'SumInsuredID' => !$additional_sum->manual ? $additional_sum->ext_id : '',
                    'SumInsured' => $additional_sum->manual ? $additional_sum->sum : ''
                ];
            }
        }

        return $risks;
    }

    /**
     * Обработка страхователя
     * 
     * @param TravelForm $form
     *
     * @return array
     */
    public function getInsurer(TravelForm $form) {
        $insurer = [
            'Name' => 'Test Testov',
            'BirthDate' => '1980-01-01',
            'IsPublicPosition' => 'Не является',
            'Position' => ''
        ];

        if ($form->payer) {
            if ($form->payer->first_name) {
                $insurer['Name'] = $form->payer->first_name . ($form->payer->last_name ? ' ' . $form->payer->last_name : '');
            }
            if ($form->payer->birthday) {
                $insurer['BirthDate'] = Yii::$app->formatter->asDate($form->payer->birthday, 'php:Y-m-d');
            }
            if ($form->payer->phone) {
                $insurer['Phone'] = $form->payer->phone;
            }
        }

        return $insurer;
    }

    /**
     * Обработка застрахованных
     * 
     * @param TravelForm $form
     *
     * @return array
     */
    public function getInsureds(TravelForm $form) {
        $insureds = [];
        $insured = [
            'Name' => 'Test Testov',
            'BirthDate' => '1980-01-01',
            'BaggageNumber' => 1,
            'IsPublicPosition' => 'Не является',
            'Position' => ''
        ];

        for ($i = 0; $i < $form->travellersCount; $i++) {
            $insureds[] = $insured;
        }

        if (!empty($form->travellers)) {
            foreach ($form->travellers as $k => $v) {
                if ($v->first_name) {
                    $insureds[$k]['Name'] = $v->first_name . ($v->last_name ? ' ' . $v->last_name : '');
                }
                if ($v->birthday) {
                    $insureds[$k]['BirthDate'] = Yii::$app->formatter->asDate($v->birthday, 'php:Y-m-d');
                }
            }
        }

        return $insureds;
    }

    /**
     * @inheritdoc
     */
    public static function getAdminMenu() {
        return [
            'label' => Yii::t('backend', 'Росгосстрах'),
            'url' => '#',
            'icon' => '<i class="fa fa-address-book"></i>',
            'options' => ['class' => 'treeview'],
            'items' => [
                ['label' => Yii::t('backend', 'Продукты'), 'url' => ['/rgs-product/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Справочники'), 'url' => ['/rgs-classifier/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Валюты'), 'url' => ['/rgs-currency/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Территории'), 'url' => ['/rgs-territory/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Страны'), 'url' => ['/rgs-country/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Виды рисков'), 'url' => ['/rgs-risk-type/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Програмы страхования'), 'url' => ['/rgs-program/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Страховые суммы'), 'url' => ['/rgs-sum/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Виды доп. условий'), 'url' => ['/rgs-additional-condition-type/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Доп. условия'), 'url' => ['/rgs-additional-condition/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>']
            ]
        ];
    }

}
