<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff;

use common\components\ApiModule;
use common\models\Currency;
use common\models\Orders;
use common\modules\ApiTinkoff\components\ProgramSearch;
use common\components\Calculator\forms\TravelForm;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\modules\ApiTinkoff\models\Price;
use Yii;
use yii\base\Exception;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * Class Module Модуль страховой компании Тинькофф
 * @package common\modules\ApiTinkoff
 */
class Module extends ApiModule
{
    /**
     * @var array окружения
     */
    public $environments = [
	  self::ENV_TEST => [
		  'basic' => 'https://tstrp.tinkoffinsurance.ru:23001/toi/partners/quotesub/v1.0',
		  'lead'  => 'https://tstrp.tinkoffinsurance.ru:23001/toi/partners/quotesub/v1.0/assist',
	  ],
	  self::ENV_PROD => [
		  'basic' => 'https://91.194.226.70:23001/toi/partners/quotesub/v1.0',
		  'lead'  => 'https://91.194.226.70:23001/toi/partners/quotesub/v1.0/assist',
	  ]
    ];

    /**
     * @inheritdoc
     */
    public $uri      = '';
    /**
     * @var string URL API
     */
    public $uri_lead = '';
    /**
     * @var string имя пользователя
     */
    public $user;
    /**
     * @var string пароль пользователя
     */
    public $password;

	/**
	 * @inheritdoc
	 */
	public $maxTravellersCount = 5;

    /**
     * @return \SoapClient геттер клиента транспорта
     */
    public function getSoap(){
		if (!$this->_soap) {
			$context = stream_context_create([
				'ssl' => [
					// set some SSL/TLS specific options
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				]
			]);

			$this->_soap = new \SoapClient( $this->uri.'?wsdl', [
				'stream_context' => $context
			]);
		}
		return $this->_soap;
	}

    /**
     * @inheritdoc
     */
    public function init()
    {
    	$this->uri = $this->environments[getenv('TINKOFF_MODE')]['basic'];
    	$this->uri_lead = $this->environments[getenv('TINKOFF_MODE')]['lead'];
    	$this->user = getenv('TINKOFF_USER');
    	$this->password = getenv('TINKOFF_PASSWORD');

        parent::init();

        // custom initialization code goes here
    }

    /**
     * Получение продуктов из АПИ
     * @return array
     * @throws Exception
     */
    public function getProducts() {
    	$responce = $this->request('getProductList');

        if (isset($responce['Header']) && isset($responce['Header']['resultInfo']) && $responce['Header']['resultInfo']['status']=='OK') {
            $products = [];
			foreach ($responce as $key=>$data) {
				if ($key=='ProductInfo') {
					$products[] = $data;
				}
            }
            return $products;
        } else throw new Exception("Error retrieving products", 500);
    }

    /**
     * Получение аттрибутов продукта из АПИ
     * @param $product_name
     *
     * @return mixed
     * @throws Exception
     */
    public function getProduct($product_name) {
		$responce = $this->request( 'getProductInfo', ['ProductName' => $product_name]);
		if (isset($responce['Header']) && isset($responce['Header']['resultInfo']) && $responce['Header']['resultInfo']['status']=='OK' && isset($responce['ProductInfo'])) {
			return $responce['ProductInfo'];
		} else throw new Exception("Error retrieving product ".$product_name, 500);
	}

    /**
     * тестовый метод расчета стоимости
     * @return mixed
     */
    public function testCalcQuote() {
		$order_info = [
			'ProductName' => 'PartnerTravelV2BulloIns',
			'ProductOptions' => [
				[
					'Code' => 'QuoteRequest',
					'ValueInfo' => [
						[
							'Code' => 'Area',
							'Value' => 'worldwide'
						],
						[
							'Code' => 'AssistanceLevel',
							'Value' => 'Basic'
						],
						[
							'Code' => 'QuantityChildren',
							'Value' => 0
						],
						[
							'Code' => 'QuantityAdults',
							'Value' => 1
						],
						[
							'Code' => 'QuantitySeniors',
							'Value' => 0
						],
						[
							'Code' => 'Currency',
							'Value' => 'eur'
						]
					],
					'Option' => [
						[
							'Code' => 'Country',
							'ValueInfo' => [
								[
									'Code' => 'Country',
									'Value' => 'AZ'
								],
								[
                                    'Code'  => 'Country',
                                    'Value' => 'SV'
                                ]
							]
						],
						[
							'Code' => 'Coverages',
							'Option' => [
								'Code' => 'TravelMedicine',
								'ValueInfo' => [
									[
										'Code' => 'TravelMedicineLimit',
										'Value' => '50000'
									]
								]
							]
						],
						[
							'Code' => 'LeisureType',
							'Option' => [
								'Code' => 'ActiveLeisure',
							]
						],
						[
							'Code' => 'TripDuration',
							'Option' => [
								'Code' => 'SingleTrip',
								'ValueInfo' => [
									[
										'Code' => 'TripStartDate',
										'Value' => '2017-09-25T00:00:00+03:00'
									],
									[
										'Code' => 'TripEndDate',
										'Value' => '2017-09-26T00:00:00+03:00'
									]
								]
							]
						]
					]
				]
			]
		];

		return $this->request('calcQuote', $order_info);
	}

    /**
     * тестовый метод оформления полиса
     */
    public function testCreatePolicy() {

	}

    /**
     * @inheritdoc
     */
    public static function getAdminMenu(){
		return [
			'label'=>Yii::t('backend', 'Тинькофф'),
			'url' => '#',
			'icon'=>'<i class="fa fa-address-book"></i>',
			'options'=>['class'=>'treeview'],
			'items'=>[

				['label'=>Yii::t('backend', 'Продукты'), 'url'=>['/tinkoff-product/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Риски'), 'url'=>['/tinkoff-risk/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Регионы'), 'url'=>['/tinkoff-area/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Страны'), 'url'=>['/tinkoff-country/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Журнал обращений к API'), 'url'=>['/tinkoff-log/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
			]
		];
	}

    /**
     * @inheritdoc
     * @param TravelForm $form
     *
     * @return \common\models\ProgramResult|null
     */public function search( TravelForm $form ){
		$searcher = new ProgramSearch([
			'form'   => $form,
			'module' => $this
		]);

		return $searcher->findAll();
	}

	/**
     * @inheritdoc
	 * @param Price $price
	 * @param TravelForm $form
	 * @param string $calc_type
	 *
	 * @return float|int|Orders
	 * @throws Exception
	 */
	public function calcPrice($price, $form, $calc_type = self::CALC_LOCAL){
		switch ($calc_type){
			case self::CALC_LOCAL:
				return $this->getPrice($form, $price);
				break;
			case self::CALC_API:
				return $this->getOrder($form, $price->id);
				break;
			default:
				throw new Exception('Calculation type not implemented: '.$calc_type, 501);
		}
	}

    /**
     * @inheritdoc
     * @param $program_id
     *
     * @return Price
     */
    public function getProgram($program_id){
		return Price::findOne(['id' => $program_id]);
	}
    /**
     * @inheritdoc
     * @return int
     */
    public function getProgramId($program){
        return $program->id;
    }


	/**
	 * @inheritdoc
	 * @param Orders $order
	 */
	public function confirmApiPayment( Orders $order ) {
		$res = false;
		$order_responce = $order->info['responce'];

		$order_request = [
			'PolicyNumber' => $order_responce['PolicyNumber'],
			'PolicyTerm' => $order->program->product->ProductVersion,
			'Amount' => $order->price
		];

		$responce = $this->request('policyPaymentConfirmV2', $order_request);

		$order_info = $order->info;
		$order_info['request_pay'] = $order_request;
		$order_info['responce_pay'] = $responce;
		$order->info = $order_info;

		if (isset($responce['Header']) && isset($responce['Header']['resultInfo']) && $responce['Header']['resultInfo']['status']=='OK'){
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

			$body = \Yii::$app->controller->renderFile('@common/modules/ApiTinkoff/views/email/order.php', [
				'site' => getenv('FRONTEND_URL'),
				'name' => $policy_holder->first_name,
				'policy' => $policy_url,
				'rule' => $order->program->program->rule_base_url."/".$order->program->program->rule_path
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
     * @param Orders $order
     * @param null $additionalInfo
     */
    public function downloadOrder(Orders $order, $additionalInfo=null){
		$log = [];
		if ($order->status == Orders::STATUS_PAYED_API) {
			$log[time()] = 'Старт загрузки информации из апи';

			$order_request = $order->info['request_pay'];
			$policy_request = array(
				'PolicyNumber' => $order_request['PolicyNumber'],
				'DocumentType' => 'policy'
			);

			$integrationID = Yii::$app->security->generateRandomString(20);

			$parameters = array_merge([
				'Header' => [
					'integrationID' => $integrationID,
					'user' => $this->user,
					'password' => $this->password
				]
			], $policy_request);


			try {
				$context = stream_context_create([
					'ssl' => [
						// set some SSL/TLS specific options
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
					]
				]);

				$soap_lead = new \SoapClient($this->uri_lead . '?wsdl', [
					'stream_context' => $context,
					'exceptions'=>false,
					'trace'=>1
				]);

				$result = $soap_lead->getPolicyDocument($parameters);

				Yii::info('getPolicyDocument Request' . PHP_EOL . htmlentities($soap_lead->__getLastRequest()), 'bull_' . $integrationID);
				Yii::info('getPolicyDocument Responce' . PHP_EOL .htmlentities($soap_lead->__getLastResponse()), 'bull_' . $integrationID);

				if (!is_soap_fault($result)) {
					$result = json_decode(json_encode((array)$result), true);

					if (isset($result['Header']) && isset($result['Header']['resultInfo']) && $result['Header']['resultInfo']['status'] == 'OK') {
						$order->is_police_downloaded = 1;
						if (!$order->save()) {
							Yii::error($this->name . " downloadOrder error" . print_r($order->getErrors(), true));
						}
					}
				}

				/*
				$responce = $order->info['responce'];
				$data = (array) $responce;
				$data = array_pop( $data );

				$url = $data->common->policyLink;

				$log[time()] = 'Сохранение полиса из '.$url;
				$this->wgetPolice($order, $url);
				*/

			} catch (Exception $e) {

			}
			$log[time() + 1] = 'Завершено';
		}
		return $log;
	}


	/**
     * Получение стоимости от АПИ
	 * @param TravelForm $form
	 * @param Price $price
	 *
	 * @return false|integer
	 */
	public function getPrice(TravelForm $form, Price $price) {
		$searcher = new ProgramSearch([
			'form'   => $form,
			'module' => $this
		]);

		$areas = [];
		$api_areas = $searcher->findAreas();
		foreach ($api_areas as $area) {
			$areas[] =  ['Code'=>'Area', 'Value'=>$area['Value']];
		}

		$countries = [];
		$api_countries = $searcher->findCountries();
		foreach ($api_countries as $country) {
			$countries[] = ['Code'=>'Country', 'Value'=>$country['Value']];
		}

		$countries_option = [];
		if (count($countries)>0) {
			$countries_option[] = [
				'Code' => 'Country',
				'ValueInfo' => $countries
			];
		}

		$sport = false;
		foreach($form->params as $param) {
			if ($param->handler->slug == FilterParamPrototype::SLUG_SPORT && $param->handler->checked) {
				$sport = true;
			}
		}

		$coverages = $searcher->findCoverages($price);

		$clients = [];

		$kol_children = 0;
		$kol_adults = 0;
		$kol_senior = 0;
		if (count($form->travellers)==$form->travellersCount) {
			$now_dt   = new \DateTime('today');
			foreach($form->travellers as $traveller){
				$birth_dt = \DateTime::createFromFormat('Y-m-d', $traveller->birthday);
				$age =  $birth_dt->diff($now_dt)->y;

				if ($age<=12) {
					$kol_children++;
				} elseif ($age>60) {
					$kol_senior++;
				} else {
					$kol_adults++;
				}

				$clients[] = [
						'Code' => 'Travellers',
						'ValueInfo' => [
							[
								'Code' => 'Age',
								'Value' => $age
							],
							[
								'Code' => 'Gender',
								'Value' => $traveller->gender
							]
						]
					];
			}
		} else {
			$kol_adults = $form->travellersCount;
		}

		$order_info = [
			'ProductName' => $price->product->Name,
			'ProductOptions' => [
				[
					'Code' => 'QuoteRequest',
					'ValueInfo' => array_merge($areas, [
						[
							'Code' => 'AssistanceLevel',
							'Value' => $price->AssistanceLevel
						],
						[
							'Code' => 'QuantityChildren',
							'Value' => $kol_children
						],
						[
							'Code' => 'QuantityAdults',
							'Value' => $kol_adults
						],
						[
							'Code' => 'QuantitySeniors',
							'Value' => $kol_senior
						],
						[
							'Code' => 'Currency',
							'Value' => $price->Currency
						]
					]),
					'Option' => array_merge($countries_option,[
						[
							'Code' => 'Coverages',
							'Option' =>  array_merge($coverages,[
								[
									'Code' => 'TravelMedicine',
									'ValueInfo' => [
										[
											'Code' => 'TravelMedicineLimit',
											'Value' => $price->TravelMedicineLimit
										],
										[
											'Code' => 'DeductibleAmount',
											'Value' => $price->DeductibleAmount
										]
									]
								]
							]),
						],
						[
							'Code' => 'LeisureType',
							'Option' => [
								'Code' => ($sport)?'ActiveLeisure':'NonActiveLeisure',
							]
						],
						[
							'Code' => 'TripDuration',
							'Option' => [
								'Code' => 'SingleTrip',
								'ValueInfo' => [
									[
										'Code' => 'TripStartDate',
										'Value' => \DateTime::createFromFormat('d.m.Y', trim($form->dateFrom))->format(\DateTime::ATOM)
									],
									[
										'Code' => 'TripEndDate',
										'Value' => \DateTime::createFromFormat('d.m.Y', trim($form->dateTo))->format(\DateTime::ATOM)
									]
								]
							]
						]
					],$clients)
				]
			]
		];

		$responce = $this->request('calcQuote', $order_info);
		if (isset($responce['Header']) && isset($responce['Header']['resultInfo']) && $responce['Header']['resultInfo']['status']=='OK' && isset($responce['Quote'])) {
			return $responce['Quote']['TotalCost'];
		} else return 0;
	}
	/**
     * @inheritdoc
	 * @param TravelForm $form
	 * @param integer $program_id
	 *
	 * @return false|Orders
	 */
	public function getOrder(TravelForm $form, $program_id){
		$price = $this->getProgram($program_id);

		$searcher = new ProgramSearch([
			'form'   => $form,
			'module' => $this
		]);
		$areas = [];
		$api_areas = $searcher->findAreas();
		foreach ($api_areas as $area) {
			$areas[] =  ['Code'=>'Area', 'Value'=>$area['Value']];
		}

		$countries = [];
		$api_countries = $searcher->findCountries();
		foreach ($api_countries as $country) {
			$countries[] = ['Code'=>'Country', 'Value'=>$country['Value']];
		}

		$countries_option = [];
		if (count($countries)>0) {
			$countries_option[] = [
				'Code' => 'Country',
				'ValueInfo' => $countries
			];
		}

		$sport = false;
		foreach($form->params as $param) {
			if ($param->handler->slug == FilterParamPrototype::SLUG_SPORT && $param->handler->checked) {
				$sport = true;
			}
		}

		$coverages = $searcher->findCoverages($price);

		$clients = [];

		/** @var \common\models\Person $payer_model */
		$payer_model = $this->getHolder($form->payer);

		$kol_children = 0;
		$kol_adults = 0;
		$kol_senior = 0;
		$now_dt   = new \DateTime('today');
		/*
		$clients[] = [
			'AccountRole' => 'primaryinsured',
			'AccountType' => 'Personal',
			//'LastName' => $payer_model->last_name,
			'LastNameEng' => $payer_model->last_name,
			//'FirstName' => $payer_model->first_name,
			'FirstNameEng' => $payer_model->first_name,
			'Birthdate' => $payer_model->birthdayAsDate('Y-m-d'),
			'Email' => $payer_model->email,
			'MobilePhone' => preg_replace("/[^0-9]/","",$payer_model->phone),
			'TypePrimaryPhone' => 'MOBILE_MAIN',
			'Gender' => 'male'
		];

		$birth_dt = \DateTime::createFromFormat('Y-m-d', $payer_model->birthday);
		$age =  $birth_dt->diff($now_dt)->y;
		if ($age<=12) {
			$role = 'Child';
			$kol_children++;
		} elseif ($age>60) {
			$role = 'Senior';
			$kol_senior++;
		} else {
			$role = 'primaryinsured';
			$kol_adults++;
		}*/

		foreach($form->travellers as $traveller){
			$birth_dt = \DateTime::createFromFormat('Y-m-d', $traveller->birthday);
			$age =  $birth_dt->diff($now_dt)->y;

			if ($age<=12) {
				$kol_children++;
			} elseif ($age>60) {
				$kol_senior++;
			} else {
				$kol_adults++;
			}

			$clients[] = [
				'AccountRole' => 'primaryinsured',
				'AccountType' => 'Personal',
				//'LastName' => $traveller->last_name,
				'LastNameEng' => $traveller->last_name,
				//'FirstName' => $traveller->first_name,
				'FirstNameEng' => $traveller->first_name,
				'Birthdate' => $traveller->birthdayAsDate('Y-m-d'),
				'Email' => $payer_model->email,
				'MobilePhone' => preg_replace("/[^0-9]/","",$payer_model->phone),
				'TypePrimaryPhone' => 'MOBILE_MAIN',
				'Gender' =>  $traveller->gender,
			];
		}

		$order_info = [
			'ProductName' => $price->product->Name,
			'ProductOptions' => [
				[
					'Code' => 'QuoteRequest',
					'ValueInfo' => array_merge($areas, [
						[
							'Code' => 'AssistanceLevel',
							'Value' => $price->AssistanceLevel
						],
						[
							'Code' => 'QuantityChildren',
							'Value' => $kol_children
						],
						[
							'Code' => 'QuantityAdults',
							'Value' => $kol_adults
						],
						[
							'Code' => 'QuantitySeniors',
							'Value' => $kol_senior
						],
						[
							'Code' => 'Currency',
							'Value' => $price->Currency
						]
					]),
					'Option' => array_merge($countries_option,[
						[
							'Code' => 'Coverages',
							'Option' =>  array_merge($coverages,[
								[
									'Code' => 'TravelMedicine',
									'ValueInfo' => [
										[
											'Code' => 'TravelMedicineLimit',
											'Value' => $price->TravelMedicineLimit
										],
										[
											'Code' => 'DeductibleAmount',
											'Value' => $price->DeductibleAmount
										]
									]
								]
							]),
						],
						[
							'Code' => 'LeisureType',
							'Option' => [
								'Code' => ($sport)?'ActiveLeisure':'NonActiveLeisure',
							]
						],
						[
							'Code' => 'TripDuration',
							'Option' => [
								'Code' => 'SingleTrip',
								'ValueInfo' => [
									[
										'Code' => 'TripStartDate',
										'Value' => \DateTime::createFromFormat('d.m.Y', trim($form->dateFrom))->format(\DateTime::ATOM)
									],
									[
										'Code' => 'TripEndDate',
										'Value' => \DateTime::createFromFormat('d.m.Y', trim($form->dateTo))->format(\DateTime::ATOM)
									]
								]
							]
						]
					])
				]
			],
			'ClientInfo' => $clients,
			'DeliveryMethod' => 'Email',
			'PaymentMethod' => 'NONE'
		];

		$responce = $this->request('CreatePolicy', $order_info);

		if (isset($responce['TotalPremium'])) {
			$order = new Orders();
			$order->api_id = $this->model->id;
			$order->price = $responce['TotalPremium'];
			$order->currency_id = Currency::findOne(['char_code' => Currency::RUR])->id;
			$order->status = Orders::STATUS_NEW;
			$order->holder_id = $payer_model->id;
			$order->info = [
				'request' => $order_info,
				'responce' => $responce,
			];
			$order->calc_form = $form;
			$order->program = $price;
			if (!$order->save()) {
				throw new Exception(strip_tags(Html::errorSummary($order)), 500);
			}

			return $order;
		} else throw new HttpException(500, 'Error retrieving result: ' . print_r($responce['Header'], true));
	}

    /**
     * Обертка запроса к АПИ
     * @param string $method
     * @param array $params
     *
     * @return mixed
     * @throws HttpException
     */
    public function request( $method, $params = [] ) {
		$integrationID = Yii::$app->security->generateRandomString(20);

		$parameters = array_merge( [
			'Header' => [
				'integrationID'=> $integrationID,
				'user' => $this->user,
				'password' => $this->password
			]
		], $params );

		Yii::info($method.' Request'.PHP_EOL.print_r($parameters,true), 'bull_'.$integrationID);
		try {
			$result = $this->soap->$method( $parameters );

			$result = json_decode(json_encode((array)$result), TRUE);
			Yii::info($method.' Responce'.PHP_EOL.print_r($result,true), 'bull_'.$integrationID);
			return $result;
		} catch (Exception $e) {
			throw new HttpException(500, $e->getMessage());
		}
	}

}