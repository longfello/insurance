<?php

namespace common\modules\ApiZetta;

use yii;
use yii\base\BootstrapInterface; // @TODO: add crontab job to update auth cookie
use yii\httpclient\Client;
use yii\helpers\FileHelper;
use yii\web\Cookie;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\models\GeoCountry;
use common\models\Currency;
use common\models\Orders;
use common\components\ApiModule;
use common\components\Calculator\forms\TravelForm;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\modules\ApiZetta\components\ProgramSearch;
use common\modules\ApiZetta\models\Product;
use common\modules\ApiZetta\models\Classifier;
use common\modules\ApiZetta\models\Country;
use common\modules\ApiZetta\models\Country2dict;
use common\modules\ApiZetta\models\CountryTerritory;
use common\modules\ApiZetta\models\ProgramSum;
use common\modules\ApiZetta\models\Gender;
use common\modules\ApiZetta\models\Sport;

class Module extends ApiModule/* implements BootstrapInterface */ {

    /**
     * Тип HTTP запроса
     */
    const HTTP_METHOD = 'post';

    /**
     * Формат данных
     */
    const DATA_FORMAT = 'json';

    /**
     * Кука авторизации
     */
    const AUTH_COOKIE = '.VFOS_t';

    /*
     * Сервис авторизации
     */
    const AUTH_SERVICE = 'auth_location';

    /*
     * API сервисы
     */
    const API_SERVICE = 'api_location';

    /*
     * Сервис печати полиса
     */
    const PDF_SERVICE = 'pdf_location';

    /**
     * @var array окружения
     */
    public $environments = [
        self::ENV_TEST => [
            'uri' => 'https://b2btest.zettains.ru/',
            'auth_location' => 'Authentication_JSON_AppService.axd/Login',
            'api_location' => 'Companies/Zetta/Travel_v3/Resources/api.vlib',
            'pdf_location' => 'ZurichCASCOPolicyFeature/WebPrint2.cmd',
            'userName' => 'BulloSafe',
            'password' => 'nX9dqdv2x2'
        ],
        self::ENV_PROD => []
    ];

    /**
     * @inheritdoc
     */
    public $uri;

    /**
     * @var string Путь до сервиса авторизации
     */
    public $auth_location;

    /**
     * @var string Путь до сервисов API
     */
    public $api_location;

    /**
     * @var string Путь до сервиса печати
     */
    public $pdf_location;

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
     * @var int Порядковый номер запроса
     */
    public $tid = 0;

    /**
     * @var Product Продукт
     */
    public $product = null;

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        foreach ($this->environments[getenv('ZETTA_MODE')] as $k => $v) {
            $this->$k = $v;
        }

        $this->product = $this->getProduct();

        $this->loadAuthCookie();
    }

    /**
     * @inheritdoc
     */
    /*
    public function bootstrap($app) {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'common\modules\ApiZetta\commands';
        }
    }
    */

    /**
     * Получение продукта страхования (ВЗР 3.0)
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
     *
     * @return yii\httpclient\Request
     */
    private function getRequest($service = self::API_SERVICE) {
        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport'
        ]);
        $request = $client->createRequest()
                ->setMethod(self::HTTP_METHOD)
                ->setFormat(self::DATA_FORMAT)
                ->setOptions([
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ])
                ->setUrl($this->uri . $this->$service);

        return $request;
    }

    /**
     * Авторизация API
     * 
     * @return true
     * 
     * @throws Exception
     */
    private function apiLogin() {
        $response = $this->getRequest(self::AUTH_SERVICE)->setData([
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
     * @return Array|null
     * 
     * @throws Exception
     */
    public function apiRequest($apiMethod, $data = [], $forceLogin = false) {
        if (!strpos($apiMethod, '.')) {
            throw new Exception('Invalid method name', 400);
        }

        list($action, $method) = explode('.', $apiMethod);
        $this->tid++;
        $requestData = [
            'action' => $action,
            'method' => $method,
            'data' => !empty($data) ? [$data] : [],
            'type' => 'rpc',
            'tid' => $this->tid
        ];

        if (is_null($this->authCookie) || $forceLogin) {
            $this->apiLogin();
        }

        $cookie = new Cookie();
        $cookie->name = self::AUTH_COOKIE;
        $cookie->value = $this->authCookie;
//if ($apiMethod == 'TravelV3.Calculate') {
//    $request = $this->getRequest()->setCookies([$cookie])->setData($requestData);
//    echo $request, PHP_EOL;
//    $response = $request->send();
//    echo print_r($response), PHP_EOL;
//    exit;
//}
        $response = $this->getRequest()->setCookies([$cookie])->setData($requestData)->send();
        // устанавливаем формат ответа вручную, так как в ответ приходит Content-Type: text/javascript
        $response->setFormat(self::DATA_FORMAT);

        // Check http code
        $http_code = $response->getHeaders()->get('http-code', null, false);
        if (!is_array($http_code) || !in_array("200", $http_code)) {
            if ($forceLogin) {
                throw new Exception('Request failed with status code ' . $http_code[0], $http_code[0]);
            }

            return $this->apiRequest($apiMethod, $data, true);
        }

        $result = $response->getData()[0]['result'];

        return $result;
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
     * @return ActiveRecord|null
     */
    public function findClassifier($class) {
        return Classifier::findOne(['class' => '\common\modules\ApiZetta\models\\' . $class]);
    }

    /**
     * Получение данных справочника через апи
     * 
     * @return Array|null
     */
    public function loadClassifierFromApi($id) {
        return $this->apiRequest('Classifier.LoadClassifier', [
            'FieldList' => ['ID', 'Name', 'Value1'],
            'ProductID' => $this->product->ext_id,
            'ClassifierID' => $id,
            'page' => 1,
            'start' => 0,
            'limit' => 255
        ]);
    }

    /**
     * Получение списка стран через апи
     * 
     * @return Array|null
     */
    public function getCountriesFromApi() {
        $classifier = $this->findClassifier('Country');

        if ($classifier !== null) {
            $result = $this->loadClassifierFromApi($classifier->ext_id);

            if ($result['count'] && !empty($result['data'])) {
                return $result['data'];
            }
        }

        return null;
    }

    /**
     * Получение списка валют страны через апи
     * 
     * @return Array|null
     */
    public function getCountryCurrenciesFromApi(Country $country) {
        $apiResult = $this->apiRequest('TravelV3.GetCurrencyList', [
            'CountriesList' => [$country->ext_id],
            'ProductID' => $this->product->ext_id
        ]);

        if ($apiResult['success'] && !empty($apiResult['data'])) {
            return $apiResult['data'];
        }

        return null;
    }

    /**
     * Получение соответствий стран - территорий через апи
     * 
     * @return Array|null
     */
    public function getCountryTerritoryFromApi() {
        $apiResult = $this->apiRequest('TravelV3.GetCountryTerritory', [
            'ProductID' => $this->product->ext_id
        ]);

        if ($apiResult['success'] && !empty($apiResult['data'])) {
            return $apiResult['data'];
        }

        return null;
    }

    /**
     * @inheritdoc
     * 
     * @param TravelForm $form
     *
     * @return \common\models\ProgramResult|null
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
     */
    public function getProgramId($program_sum) {
        return [
            'program_id' => $program_sum->program_id,
            'sum_id' => $program_sum->sum_id
        ];
    }

    /**
     * @inheritdoc
     * 
     * @param Array $ids
     *
     * @return ProgramSum
     */
    public function getProgram($ids) {
        return ProgramSum::findOne([
            'program_id' => $ids['program_id'],
            'sum_id' => $ids['sum_id']
        ]);
    }

    /**
     * @inheritdoc
     * 
     * @param ProgramSum $program_sum
     * @param TravelForm $form
     * @param string $calc_type
     *
     * @return float|int|Orders
     * 
     * @throws Exception
     */
    public function calcPrice($program_sum, $form, $calc_type = self::CALC_LOCAL) {
        switch ($calc_type) {
            case self::CALC_LOCAL:
                return $this->getPrice($form, $program_sum);
            break;
//            case self::CALC_API:
//                $order = $this->getOrder($form, $program->id);
//                return $order->price;
//            break;
            default:
                throw new Exception('Calculation type not implemented: ' . $calc_type, 501);
            break;
        }
    }

    /**
     * Получение стоимости из АПИ
     * 
     * @param TravelForm $form
     * @param ProgramSum $program_sum
     *
     * @return string|int 0
     */
    public function getPrice(TravelForm $form, ProgramSum $program_sum) {
        $data = $this->getData($form, $program_sum);

        try {
            $result = $this->apiRequest('TravelV3.Calculate', $data);
            if ($result['success'] && !empty($result['data']) && $result['data']['PremiumRUR']) {
                return number_format((float) $result['data']['PremiumRUR'], 2, '.', '');
            } else {
//                return $result['message'];
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     * 
     * @param TravelForm $form
     * @param array $ids
     *
     * @return false|Orders
     * @throws Exception
     */
    public function getOrder(TravelForm $form, $ids) {
        /** @var ProgramSum $program_sum */
        $program_sum = $this->getProgram($ids);

        /** @var \common\models\Person $payer_model */
        $payer_model = $this->getHolder($form->payer);

        $data = $this->getData($form, $program_sum);

        try {
            $result = $this->apiRequest('TravelV3.CalcAndSave', $data);
            if ($result['success'] && !empty($result['data']) && $result['data']['PremiumRUR']) {
                $order = new Orders();
                $order->api_id = $this->model->id;
                $order->price = number_format((float) $result['data']['PremiumRUR'], 2, '.', '');
                $order->currency_id = Currency::findOne(['char_code' => Currency::RUR])->id;
                $order->status = Orders::STATUS_NEW;
                $order->holder_id = $payer_model->id;
                $order->info = [
                    'request' => $data,
                    'responce' => $result
                ];
                $order->calc_form = $form;
                $order->program = $program_sum;
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
     * @param Orders $order
     */
    public function confirmApiPayment( Orders $order )
    {
        $res = false;

        //accept
        $result = $this->apiRequest('TravelV3.Accept', $order->info['responce']['data']);

        $order_info = $order->info;
        $order_info['request_pay'] = $order->info['responce']['data'];
        $order_info['responce_pay'] = $result;
        $order->info = $order_info;

        if ($result['success'] && !empty($result['data'])) {
            $res = true;
            $order->status = Orders::STATUS_PAYED_API;
        }

        if (!$order->save()) Yii::error($this->name." confirmApiPayment error".print_r($order->getErrors(), true));

        return $res;
    }

    /**
     * @inheritdoc
     * @param Orders $order
     */
    public function sendOrderMail($order) {
        if ($policy_url = $this->getPoliceLink($order)) {
            $policy_holder = $order->calc_form->payer;

            $body = \Yii::$app->controller->renderFile('@common/modules/ApiZetta/views/email/order.php', [
                'site' => getenv('FRONTEND_URL'),
                'name' => $policy_holder->first_name,
                'policy' => $policy_url,
                'rule' => $this->product->rule_base_url . "/" . $this->product->rule_path
            ]);

            \Yii::$app->mailer->compose()
                ->setTo($policy_holder->email)
                ->setFrom([env('ROBOT_EMAIL') => \Yii::$app->name . ' robot'])
                ->setSubject('Ваш страховой полис')
                ->setHtmlBody($body)
                ->send();
        }
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

            if (!$policeObj) $policeObj = (isset($order->info['responce_pay']))?$order->info['responce_pay']['data']:null;

            if ($policeObj) {
                $log[time()] = 'Старт загрузки информации из апи';

                // get pdf
                $response = $this->getRequest(self::PDF_SERVICE)->setData([
                    'userName' => $this->userName,
                    'password' => $this->password,
                    'viewKey' => 'eedfe6ad-8d7c-407b-81f7-fbd11074b4d1',
                    'policy' => json_encode($policeObj)
                ])->send();

                // Check http code
                $http_code = $response->getHeaders()->get('http-code', null, false);
                if (!is_array($http_code) || !in_array("200", $http_code)) {
                    throw new Exception('Request failed with status code ' . $http_code[0], $http_code[0]);
                }

                // Check result
                $result = $response->getData();
                if (!$result['d']['IsValid']) {
                    throw new Exception('Invalid username or password', 400);
                }

                $log[time()] = 'Получен ответ: <pre>' . print_r($result, true) . '</pre>';

                $pdf = $result['d']['Result']['pdf'];
                $folder = $this->getOrderFolder($order);
                $file = $folder . 'police.pdf';

                $log[time()] = 'Сохранение полиса из base64 в ' . $file;

                file_put_contents($file, base64_decode($pdf));

                $log[time() + 1] = 'Завершено';

                $order->is_police_downloaded = 1;
                if (!$order->save()) Yii::error($this->name." downloadOrder error".print_r($order->getErrors(), true));
            }
        }

        return $log;
    }

    /**
     * Формирование объекта полиса
     * 
     * @param TravelForm $form
     * @param ProgramSum $program_sum
     *
     * @return array|null
     */
    public function getData(TravelForm $form, ProgramSum $program_sum) {
        try {
            list($countries, $territory) = $this->getCountries($form);
            $countriesList = [];
            $countriesListRaw = '';
            if (!empty($countries)) {
                $countriesList = ArrayHelper::getColumn($countries, 'ext_id');
                $countriesListRaw = implode(', ', ArrayHelper::getColumn($countries, 'title'));
            }
            $territoriesListRaw = '';
            if ($territory !== null) {
                $countriesList[] = $territory->ext_id;
                $territoriesListRaw = $territory->title;
            }

            $program = $program_sum->programModel;
            $sum = $program_sum->sumModel;
            $currency = $sum->currencyModel;

            /**
             * @todo Get accident sums from api
             */
            $accidentSums = [
                1 => 1000,
                2 => 5000,
                3 => 10000
            ];
            $accidentSum = null;
            foreach ($form->params as $param) {
                if ($param->handler->checked) {
                    switch ($param->handler->slug) {
                        case FilterParamPrototype::SLUG_ACCIDENT:
                            $costInterval = \common\models\CostInterval::find()->alias('ci')->leftJoin(['azs2d' => models\Sum2dict::tableName()], 'ci.id = azs2d.internal_id')->where(['azs2d.sum_id' => $sum->id])->one();
                            $accidentSum = $accidentSums[$costInterval->id];
                        break;
                    }
                }
            }

            return [
                'ProductID' => $this->product->ext_id,
                'BSOType' => 'f623c653-3e35-460b-9f93-8ea5a2c84183', // БСО для тестов
//                'BSOType' => 'f30b3731-4704-487f-8559-951467e175f9', // электронный полис
                'SERIAL' => 'TRV', // для тестов
                'NUMBER' => time(), // для тестов
                'NUMBAR' => time(), // для тестов
                'DocumentDate' => date('Y-m-d'),
                'CountriesList' => $countriesList,
                'CountriesListRaw' => $countriesListRaw,
                'TerritoriesListRaw' => $territoriesListRaw,
                'Currency' => $currency->ext_id,
                'CurrencyRaw' => $currency->title,
                'EffectiveDate' => Yii::$app->formatter->asDate($form->dateFrom, 'php:Y-m-d'),
                'ExpirationDate' => Yii::$app->formatter->asDate($form->dateTo, 'php:Y-m-d'),
                'Duration' => $form->dayCount,
                'Program' => $program->ext_id,
                'ProgramRaw' => $program->title,
                'SumMedicine' => $sum->sum,
                'SumAccident' => $accidentSum,
                'Insurer' => $this->getInsurer($form),
                'InsuredsCount' => $form->travellersCount,
                'Insured' => $this->getInsureds($form)
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Обработка выбранных стран
     * 
     * @param TravelForm $form
     *
     * @return array
     * @throws Exception
     */
    public function getCountries(TravelForm $form) {
        if (empty($form->countries)) {
            throw new Exception('Страна: Это поле обязательно для заполнения');
        }

        $countries = [];
        $territory = null;
        $maxCountries = 5;

        $shengen_selected = false;
        if (!empty($form->territories)) {
            foreach ($form->territoriesModels as $territoryModel) {
                if ($territoryModel->slug == 'shengen') {
                    // Территория шенген выбрана в форме выбора страны
                    $shengen_selected = true;

                    break;
                }
            }
        }

        $selectedCountries = count($form->countriesOverall);
        $selectedTerritories = count($form->territories);
        if ($shengen_selected) {
            $selectedTerritories--;
        }

        // Если выбрана территория не шенген или выбрано больше 5 стран
        // или выбраны страны и территории - выбираем территорию
        if ($selectedCountries > 0 && $selectedTerritories > 0) {
            return $this->processTerritoryWithCountries();
        } else if ($selectedCountries > $maxCountries) {
            return $this->processTerritoryByCountries($form);
        } else if ($selectedTerritories > 0) {
            return $this->processTerritory($form);
        }

        // Получаем территорию шенген
        $shengen = Country::getShengen();

        if ($selectedCountries > 0) {
            $q = (new Query())
                    ->select(['c.id c_id', 'c.ext_id c_ext_id', 'c.title c_title', 'c.type c_type', 'c.enabled c_enabled', 't.id t_id', 't.ext_id t_ext_id', 't.title t_title', 't.type t_type', 't.enabled t_enabled'])
                    ->from(Country::tableName() . ' c')
                    ->innerJoin(Country2dict::tableName() . ' c2d', 'c2d.country_id = c.id')
                    ->innerJoin(CountryTerritory::tableName() . ' ct', 'c.id = ct.country_id')
                    ->innerJoin(Country::tableName() . ' t', 'ct.territory_id = t.id')
                    ->where(['c2d.internal_id' => $form->countriesOverall, 'c.type' => GeoCountry::TYPE_COUNTRY, 'c.enabled' => 1, 't.enabled' => 1]);
            $res = ArrayHelper::index($q->all(), null, ['c_ext_id']);
//echo $q->createCommand()->getRawSql(), PHP_EOL;
            // Если выбрана хотя бы одна страна не входящая в список разрешенных - ошибка
            if ($selectedCountries > count($res)) {
                throw new Exception('Не все выбранные страны найдены или разрешены');
            }

            foreach ($res as $territories) {
                $countryModel = null;

                foreach ($territories as $countryTerritory) {
                    if ($countryModel === null) {
                        $countryModel = new Country([
                            'id' => $countryTerritory['c_id'],
                            'ext_id' => $countryTerritory['c_ext_id'],
                            'title' => $countryTerritory['c_title'],
                            'type' => $countryTerritory['c_type'],
                            'enabled' => $countryTerritory['c_enabled']
                        ]);
                        $countries[] = $countryModel;
                    }

                    if (!$shengen_selected && $countryTerritory['t_id'] == $shengen->id) {
                        // Выбрана страна входящая в территорию шенген
                        $shengen_selected = true;

                        break;
                    }
                }
            }
        }

        // Если выбрана хотя бы одна страна из территории "ШЕНГЕН" - 
        // добавлять территорию в список не зависимо от кол-ва выбранных стран.
        if ($shengen_selected) {
            $territory = $shengen;
        }

        return [$countries, $territory];
    }

    /**
     * Обработка выбранных стран и территорий
     * 
     * @throws Exception
     */
    private function processTerritoryWithCountries() {
        throw new Exception('Страна: Выберите либо набор стран (включая Шенген) либо одну из территорий');
    }

    /**
     * Обработка территории если выбрано больше 5 стран
     * 
     * @param TravelForm $form
     * 
     * @return Array
     * @throws Exception
     */
    private function processTerritoryByCountries(TravelForm $form) {
        $territory = null;
        $selectedCountries = count($form->countriesOverall);

        $q = (new Query())
                ->select('azc1.*, azc.id country_id')
                ->from(['azc' => Country::tableName()])
                ->innerJoin(Country2dict::tableName() . ' azc2d', 'azc.id = azc2d.country_id')
                ->innerJoin(CountryTerritory::tableName() . ' azct', 'azc.id = azct.country_id')
                ->innerJoin(Country::tableName() . ' azc1', 'azct.territory_id = azc1.id')
                ->where([
                    'azc2d.internal_id' => $form->countries,
                    'azc.enabled' => 1,
                    'azc1.enabled' => 1
                ])
                ->orderBy([
            'azc1.id' => SORT_ASC
        ]);

        $res = ArrayHelper::index($q->all(), null, 'ext_id');

        foreach ($res as $territory_countries) {
            if (count($territory_countries) == $selectedCountries) {
                $territory = new Country([
                    'id' => $territory_countries[0]['id'],
                    'ext_id' => $territory_countries[0]['ext_id'],
                    'title' => $territory_countries[0]['title'],
                    'type' => $territory_countries[0]['type'],
                    'enabled' => $territory_countries[0]['enabled']
                ]);

                break;
            }
        }

        if (empty($territory)) {
            throw new Exception('Не найдено подходящих территорий');
        }

        return [[], $territory];
    }

    /**
     * Обработка выбранных территорий
     * 
     * @param TravelForm $form
     * 
     * @return Array
     * @throws Exception
     */
    private function processTerritory(TravelForm $form) {
        $territories = [];
        foreach ($form->territoriesModels as $territoryModel) {
            if ($territoryModel->slug !== 'shengen') {
                $territories[] = $territoryModel->id;
            }
        }

        $territory = Country::find()
                        ->alias('azc')
                        ->innerJoin(Country2dict::tableName() . ' azc2d', 'azc.id = azc2d.country_id')
                        ->where([
                            'azc.enabled' => 1,
                            'azc2d.internal_id' => $territories
                        ])->one();

        if (empty($territory)) {
            throw new Exception('Выбранные территории не найдены');
        }

        return [[], $territory];
    }

    /**
     * Обработка страхователя
     * 
     * @param TravelForm $form
     * 
     * @return Array
     */
    public function getInsurer(TravelForm $form) {
        // @TODO: уточнить про гражданство, если поле обязательное - 
        // то нужно дополнительное поле в форму страхователя
        // пока что передаем по умолчанию РФ
        $default = [
            'Resident' => true,
            'Citizenship' => 'e1a1c097-c190-487e-b629-34bce3a0e441', //Россия
            'CitizenshipRaw' => 'Россия',
            'Type' => '23aeef77-a2b7-5c1c-927d-f431a58c4ee4', //Физическое лицо
            'Surname' => 'Иванов',
            'Name' => 'Иван',
            'Patronymic' => '',
            'Fullname' => 'Иванов Иван',
            'BirthDate' => '1980-01-01', //80го года - дешевле
            'Sex' => '1276ffb4-8047-a48f-94da-27f9c22754bd', //Женский - дешевле мужского
            'SexRaw' => 'Женский',
            'DocumentType' => 'b3213c1b-4584-4aa3-b4a6-4e06f952e02f', //Загран паспорт
            'PassportNumber' => '11 1111111', //Уточнить формат ввода
//            'INN' => null,
            'Phone' => '7 (555) 555-5555',
            'Email' => 'test@test.ru',
            'PDL' => '540e4e30-281b-4311-b4df-07bc4c7cd1e9', // Нет - 540e4e30-281b-4311-b4df-07bc4c7cd1e9, Да - 264b0203-43b4-4d2a-8f6d-983f8c1e613a
            'PDLPost' => '',
            'PDLRelative' => '540e4e30-281b-4311-b4df-07bc4c7cd1e9', // Нет - 540e4e30-281b-4311-b4df-07bc4c7cd1e9, Да - 264b0203-43b4-4d2a-8f6d-983f8c1e613a
            'PDLRelativePost' => ''
        ];

        if ($form->payer) {
            if ($form->payer->first_name) {
                $default['Name'] = $form->payer->first_name;
            }
            if ($form->payer->last_name) {
                $default['Surname'] = $form->payer->last_name;
            }
            if ($form->payer->birthday) {
                $default['BirthDate'] = Yii::$app->formatter->asDate($form->payer->birthday, 'php:Y-m-d');
            }
            if ($form->payer->gender) {
                $gender = Gender::findOne(['alias' => $form->payer->gender]);
                $default['Sex'] = $gender->ext_id;
                $default['SexRaw'] = $gender->title;
            }
            if ($form->payer->phone && preg_match("/\+\d\(\d{3}\)\d{3}-\d{2}-\d{2}/", $form->payer->phone)) {
                $phone = preg_replace('/(\d)(\d{3})(\d{3})(\d{4})/', '$1 ($2) $3-$4', preg_replace('/\D/', '', $form->payer->phone));
                $default['Phone'] = $phone;
            }
            if ($form->payer->email) {
                $default['Email'] = $form->payer->email;
            }
            if ($form->payer->passport_no/* && preg_match("/^\d{2}\s?\d{7}$/", $form->payer->passport_no) */) {
                $default['PassportNumber'] = $form->payer->passport_no;
            }
        }

        $default['Fullname'] = $default['Surname'] . ' ' . $default['Name'];

        return $default;
    }

    /**
     * Обработка застрахованных
     * 
     * @param TravelForm $form
     * 
     * @return Array
     */
    public function getInsureds(TravelForm $form) {
        $insureds = [];
        $sumCancel = '';
        $sport = null;

        foreach ($form->params as $param) {
            if ($param->handler->checked) {
                switch ($param->handler->slug) {
                    case FilterParamPrototype::SLUG_CANCEL:
                        $sumCancel = $param->handler->variant['amount'];
                    break;
                    case FilterParamPrototype::SLUG_SPORT:
                        $sport = Sport::find()->where([
                            'enabled' => 1
                        ])->andWhere([
                            '<>', 'title', 'No Sport'
                        ])->one();
                    break;
                }
            }
        }

        if ($sport === null) {
            $sport = Sport::findOne([
                'title' => 'No sport',
                'enabled' => 1
            ]);
        }

        $default = [
            'Resident' => true,
            'Citizenship' => 'e1a1c097-c190-487e-b629-34bce3a0e441', //Россия
            'Surname' => 'Иванов',
            'Name' => 'Иван',
            'Patronymic' => '',
            'Sex' => '1276ffb4-8047-a48f-94da-27f9c22754bd', //Женский - дешевле мужского
            'BirthDate' => '1980-01-01', //80го года - дешевле
//            'DocumentType' => 'a87b6e82-0532-499e-8cbe-b76bcdcb8138',       //Паспорт гражданина РФ
//            'DocumentNumber' => '1111111111',                               //Уточнить формат ввода
            'DocumentType' => '00000000-0000-0000-0000-000000000000', //Паспорт гражданина РФ
            'DocumentNumber' => '', //Уточнить формат ввода
            'Sport' => $sport->ext_id,
            'SportRaw' => $sport->title,
            'SumCancell' => $sumCancel,
//            'Priora' => false
        ];

        for ($i = 0; $i < $form->travellersCount; $i++) {
            $insureds[] = $default;
        }

        if (!empty($form->travellers)) {
            foreach ($form->travellers as $k => $v) {
                if ($v->first_name) {
                    $insureds[$k]['Name'] = $v->first_name;
                }
                if ($v->last_name) {
                    $insureds[$k]['Surname'] = $v->last_name;
                }
                if ($v->birthday) {
                    $insureds[$k]['BirthDate'] = Yii::$app->formatter->asDate($v->birthday, 'php:Y-m-d');
                }
                if ($v->gender) {
                    $insureds[$k]['Sex'] = Gender::findOne(['alias' => $v->gender])->ext_id;
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
            'label' => Yii::t('backend', 'Зетта Страхование'),
            'url' => '#',
            'icon' => '<i class="fa fa-address-book"></i>',
            'options' => ['class' => 'treeview'],
            'items' => [
                ['label' => Yii::t('backend', 'Продукты'), 'url' => ['/zetta-product/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Справочники'), 'url' => ['/zetta-classifier/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Валюты'), 'url' => ['/zetta-currency/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Страны'), 'url' => ['/zetta-country/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Риски'), 'url' => ['/zetta-risk/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Програмы страхования'), 'url' => ['/zetta-insurance-programm/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Спорт'), 'url' => ['/zetta-sport/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Страховые суммы'), 'url' => ['/zetta-sum/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ['label' => Yii::t('backend', 'Суммы покрытия'), 'url' => ['/zetta-risk-sum/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>']
            ]
        ];
    }

}
