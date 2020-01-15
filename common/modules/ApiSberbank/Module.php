<?php
/**
 * Copyright (c) kvk-group 2018.
 */

namespace common\modules\ApiSberbank;

use common\components\ApiModule;
use common\models\Currency;
use common\models\Orders;
use common\models\Person;
use frontend\models\PersonInfo;
use common\modules\ApiSberbank\models\Program;
use common\modules\ApiSberbank\components\ProgramSearch;
use common\components\Calculator\forms\TravelForm;
use common\modules\ApiSberbank\models\AdditionalRisk;
use common\modules\ApiSberbank\models\Territory2Dict;
use common\modules\geo\models\GeoCountry;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\httpclient\Client;

/**
 * Class Module Модуль страхования Сбербанк
 * @package common\modules\ApiSberbank
 */
class Module extends ApiModule
{
    const OPTION_SPORT        = 'isOptionSport';
    const OPTION_BAGGAGE      = 'isOptionBaggage';
    const OPTION_SPECIAL_CASE = 'isOptionSpecialCase';
    const OPTION_LAWYER       = 'isOptionLawyer';
    const OPTION_PRUDENT      = 'isOptionPrudent';

    /**
     * @inheritdoc
     */
	public $api_url = 'https://dev.sberbankins.ru/ws/rest/loader/';


	/**
	 * @var string имя пользователя
	 */
	public $user_login;

	/**
	 * @var string пароль пользователя
	 */
	public $user_token;

    /**
     * @inheritdoc
     */
    public function init()
    {
		$this->user_login = getenv('SBERBANK_LOGIN');
		$this->user_token = getenv('SBERBANK_TOKEN');

        parent::init();

        // custom initialization code goes here
    }

	/**
	 * тестовый метод расчета стоимости
	 * @return mixed
	 */
	public function testCalcObject() {

		$order_data = array(
			"dateBeginTravel" => "2018-1-26",
			"dateEndTravel" => "2018-1-30",
			"insTerritory" => "00001",
			"insProgram" => "00001",
			"isYearContract" => false,
			"countOfDays" => 6,
			"insuredGroup1" => 1,
			"insuredGroup2" => 0,
			"insuredGroup3" => 0,
			"isOptionSport" => false,
			"isOptionBaggage" => false,
			"isOptionSpecialCase" => false,
			"isOptionLawyer" => false,
			"isOptionPrudent" => false
		);

		$header = array(
			'userLogin'=>$this->user_login,
			'userToken'=>$this->user_token,
			'templateID'=>'103',
			'subTemplate'=>'null'
		);


		$client = new Client([
			'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
		]);

		$response = $client->createRequest()
			->setFormat(Client::FORMAT_JSON)
			->setHeaders($header)
			->setMethod('post')
			->setUrl($this->api_url."calcObject")
			->setData($order_data)
			->setOptions([
				CURLOPT_CONNECTTIMEOUT => 10, // connection timeout
				CURLOPT_TIMEOUT => 50, // data receiving timeout
			])
			->send();

		if ($response->isOk) {
			return $response->data;
		} else return $response->content;
	}

    /**
     * @inheritdoc
     */
    public static function getAdminMenu(){
		return [
			'label'=>Yii::t('backend', 'Сбербанк'),
			'url' => '#',
			'icon'=>'<i class="fa fa-address-book"></i>',
			'options'=>['class'=>'treeview'],
			'items'=>[
				['label'=>Yii::t('backend', 'Програмы страхования'), 'url'=>['/sberbank-program/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Территории страхования'), 'url'=>['/sberbank-territory/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Риски'), 'url'=>['/sberbank-risk/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Доп. риски'), 'url'=>['/sberbank-additional-risk/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
			]
		];
	}

    /**
     * @inheritdoc
     * @param TravelForm $form
     *
     * @return mixed
     */public function search( TravelForm $form ){
		$searcher = new ProgramSearch([
			'form'   => $form,
			'module' => $this
		]);

		return $searcher->findAll();
	}

	/**
     * @inheritdoc
	 * @param Program $program
	 * @param TravelForm $form
	 * @param string $calc_type
	 *
	 * @return float|int
	 * @throws Exception
	 */
	public function calcPrice($program, $form, $calc_type = self::CALC_LOCAL){
		switch ($calc_type){
			case self::CALC_LOCAL:
				return $this->getPrice($form, $program);
				break;
			case self::CALC_API:
				return $this->getOrder($form, $program->id);
				break;
			default:
				throw new Exception('Calculation type not implemented: '.$calc_type, 501);
		}
	}

    /**
     * @inheritdoc
     * @param $program_id
     *
     * @return mixed
     */
    public function getProgram($program_id){
		return Program::findOne(['id' => $program_id]);
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
	public function confirmApiPayment( Orders $order )
	{
		$res = false;

		$responce = $order->info['responce'];

		$order_request = [
			"contractID" => $responce['result']['data']['contractID'],
			"payAmount" => $order->price,
			"payDate" => date(DATE_W3C)
		];

		$header = array(
			'userLogin'=>$this->user_login,
			'userToken'=>$this->user_token,
			'templateID'=>'102',
			'subTemplate'=>'null'
		);

		$order_info = $order->info;
		$order_info['request_pay'] = $order_request;

		try {
			$client = new Client([
				'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
			]);
			$response = $client->createRequest()
				->setFormat(Client::FORMAT_JSON)
				->setHeaders($header)
				->setMethod('post')
				->setUrl($this->api_url . 'loadObject')
				->setData($order_request)
				->setOptions([
					CURLOPT_CONNECTTIMEOUT => 10, // connection timeout
					CURLOPT_TIMEOUT => 50, // data receiving timeout
				])
				->send();

			$order_info['responce_pay'] = $response->data;

			if ($response->isOk && isset($response->data['status']) && $response->data['status']['code'] == 0) {
				$res = true;
				$order->status = Orders::STATUS_PAYED_API;
			}
		}catch (Exception $e) {

		}

		$order->info = $order_info;
		$order->save();

		return $res;
	}

	/**
	 * @inheritdoc
	 * @param Orders $order
	 * @param null $additionalInfo
	 *
	 * @return array
	 */
	public function downloadOrder(Orders $order, $additionalInfo=null){
		$log = [];

		return $log;
	}

	/**
	 * Получение стоимости из АПИ
	 * @param TravelForm $form
	 * @param Program $program
	 *
	 * @return false|Orders
	 */
	public function getPrice($form, Program $program) {
		$searcher = new ProgramSearch([
			'form'   => $form,
			'module' => $this
		]);

		$territory = $searcher->findTerritory($program);

		$kol_children = 0;
		$kol_adults = 0;
		$kol_senior = 0;
		if (count($form->travellers)==$form->travellersCount) {
			$now_dt   = new \DateTime('today');
			foreach($form->travellers as $traveller){
				if (!empty($traveller->birthday)) {
					$birth_dt = \DateTime::createFromFormat('Y-m-d', $traveller->birthday);
					$age = $birth_dt->diff($now_dt)->y;

					if ($age<=2) {
						$kol_children++;
					} elseif ($age>=61) {
						$kol_senior++;
					} else {
						$kol_adults++;
					}
				} else $kol_adults++;
			}
		} else {
			$kol_adults = $form->travellersCount;
		}

		$order_data = array(
			"dateBeginTravel" => \DateTime::createFromFormat('d.m.Y', trim($form->dateFrom))->format('Y-m-d'),
			"dateEndTravel" => \DateTime::createFromFormat('d.m.Y', trim($form->dateTo))->format('Y-m-d'),
			"insTerritory" => $territory->insTerritory ,
			"insProgram" => $program->insProgram,
			"isYearContract" => false,
			"countOfDays" => $form->dayCount,
			"insuredGroup1" => $kol_adults,
			"insuredGroup2" => $kol_children,
			"insuredGroup3" => $kol_senior,
			"isOptionSport"       => $this->isOptionSelected(self::OPTION_SPORT, $form),
			"isOptionBaggage"     => $this->isOptionSelected(self::OPTION_BAGGAGE, $form),
			"isOptionSpecialCase" => $this->isOptionSelected(self::OPTION_SPECIAL_CASE, $form),
			"isOptionLawyer"      => $this->isOptionSelected(self::OPTION_LAWYER, $form),
			"isOptionPrudent"     => $this->isOptionSelected(self::OPTION_PRUDENT, $form)
		);

		$header = array(
			'userLogin'=>$this->user_login,
			'userToken'=>$this->user_token,
			'templateID'=>'103',
			'subTemplate'=>'null'
		);


		try {
			$client = new Client([
				'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
			]);
			$response = $client->createRequest()
				->setFormat(Client::FORMAT_JSON)
				->setHeaders($header)
				->setMethod('post')
				->setUrl($this->api_url . 'calcObject')
				->setData($order_data)
				->setOptions([
					CURLOPT_CONNECTTIMEOUT => 10, // connection timeout
					CURLOPT_TIMEOUT => 50, // data receiving timeout
				])
				->send();

			if ($response->isOk) {
				if (isset($response->data['status']) && $response->data['status']['code']==0) {
					if (isset($response->data['result']) && isset($response->data['result']['totalRUR'])) {
						return $response->data['result']['totalRUR'];
					}
				} else return 0;
			} else return 0;
		}catch (Exception $e) {
			return 0;
		}
	}

    /**
     * Выбрана ли опция в калькуляторе (дополнительно проверяет доступность опции для текущих параметров калькулятора)
     * @param $option string
     * @param $form TravelForm
     * @return bool
     */
    public function isOptionSelected($option, $form){
        $optionModel = AdditionalRisk::findOne(['slug' => $option]);
        if ($optionModel){
            $riskTerritories = ArrayHelper::map($optionModel->territories, 'id', 'id');
            $countries = ArrayHelper::map(Territory2Dict::findAll(['territory_id' => $riskTerritories]), 'internal_id', 'internal_id');
            if (array_intersect($countries, $form->countries)){
                $risks1 = [];
				foreach($form->params as $param) {
					if ($param->handler->checked) $risks1[$param->risk_id] = $param->risk_id;
				}

                $risks2 = ArrayHelper::map($optionModel->internalRisks, 'id', 'id');
				
				if (array_intersect($risks1, $risks2)){
                    return true;
                }
            }
        }
        return false;
    }

	/**
	 * @inheritdoc
	 * @param TravelForm $form
	 * @param integer $program_id
	 *
	 * @return false|Orders
	 */
	public function getOrder(TravelForm $form, $program_id){
		/** @var $program Program */
		$program = $this->getProgram($program_id);

		/** @var \common\models\Person $payer_model */
		$payer_model = $this->getHolder($form->payer);

		$searcher = new ProgramSearch([
			'form'   => $form,
			'module' => $this
		]);

		$territory = $searcher->findTerritory($program);

		$kol_children = 0;
		$kol_adults = 0;
		$kol_senior = 0;

		$members = [];

		if (count($form->travellers)==$form->travellersCount) {
			$now_dt   = new \DateTime('today');
			foreach($form->travellers as $traveller){
				$birth_dt = \DateTime::createFromFormat('Y-m-d', $traveller->birthday);
				$age =  $birth_dt->diff($now_dt)->y;

				if ($age<=2) {
					$kol_children++;
				} elseif ($age>=61) {
					$kol_senior++;
				} else {
					$kol_adults++;
				}

				$members[] = [
					"surname"=> $traveller->last_name,
					"name"=> $traveller->first_name,
					"dateOfBirth" =>  $traveller->birthdayAsDate('Y-m-d')
				];
			}
		} else {
			$kol_adults = $form->travellersCount;
		}

		$payer_phone = preg_replace('/[^0-9]/', '', $payer_model->phone);
		$order_data = array(
			"insRegion" => "7700000000000",
			"dateBeginTravel" => \DateTime::createFromFormat('d.m.Y', trim($form->dateFrom))->format('Y-m-d'),
			"dateEndTravel" => \DateTime::createFromFormat('d.m.Y', trim($form->dateTo))->format('Y-m-d'),
			"insurer" => [
				"country" => "000",
				"surname" => $payer_model->last_name,
				"name" => $payer_model->first_name,
				"dateOfBirth" => $payer_model->birthdayAsDate('Y-m-d'),
				"sex" => $payer_model->gender,
				"mobileTel" => mb_substr($payer_phone, 1),
				"email" => $payer_model->email
			],
			"members"=> $members,
			"insTerritory" => $territory->insTerritory,
			"insProgram" => $program->insProgram,
			"isYearContract" => false,
			"countOfDays" => $form->dayCount,
			"insuredGroup1" => $kol_adults,
			"insuredGroup2" => $kol_children,
			"insuredGroup3" => $kol_senior,
			"isOptionSport"       => $this->isOptionSelected(self::OPTION_SPORT, $form),
			"isOptionBaggage"     => $this->isOptionSelected(self::OPTION_BAGGAGE, $form),
			"isOptionSpecialCase" => $this->isOptionSelected(self::OPTION_SPECIAL_CASE, $form),
			"isOptionLawyer"      => $this->isOptionSelected(self::OPTION_LAWYER, $form),
			"isOptionPrudent"     => $this->isOptionSelected(self::OPTION_PRUDENT, $form)
		);

		$header = array(
			'userLogin'=>$this->user_login,
			'userToken'=>$this->user_token,
			'templateID'=>'101',
			'subTemplate'=>'null'
		);


		try {
			$client = new Client([
				'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
			]);
			$response = $client->createRequest()
				->setFormat(Client::FORMAT_JSON)
				->setHeaders($header)
				->setMethod('post')
				->setUrl($this->api_url . 'loadObject')
				->setData($order_data)
				->setOptions([
					CURLOPT_CONNECTTIMEOUT => 10, // connection timeout
					CURLOPT_TIMEOUT => 50, // data receiving timeout
				])
				->send();

			if ($response->isOk) {
				if (isset($response->data['status']) && $response->data['status']['code']==0) {
					$order = new Orders();
					$order->api_id      = $this->model->id;
					$order->price       = $this->getPrice($form, $program);;
					$order->currency_id = Currency::findOne(['char_code' => Currency::RUR])->id;
					$order->status      = Orders::STATUS_NEW;
					$order->holder_id   = $payer_model->id;
					$order->info        = [
						'request' => $order_data,
						'responce' => $response->data,
					];
					$order->calc_form   = $form;
					$order->program     = $program;
					if (!$order->save()) {
						throw new Exception(strip_tags(Html::errorSummary($order)), 500);
					}
					return $order;
				}  else throw new HttpException(500, 'Error retrieving result: '.print_r($response->data, true));
			} else throw new HttpException(500, 'Error sending request');
		}catch (Exception $e) {
			throw new HttpException(500, $e->getMessage());
		}
	}

	/**
	 * @inheritdoc
	 *
	 * @param $info PersonInfo
	 * @return Person
	 */
	public function getHolder(PersonInfo $info){
		$holder = Person::findOne([
			'first_name' => $info->first_name,
			'last_name' => $info->last_name,
			'birthday' => $info->birthday,
		]);
		if (!$holder){
			$holder = new Person(['scenario' => Person::SCENARIO_PAYER_SBERBANK]);
			$holder->load(ArrayHelper::toArray($info), '');
			if (!$holder->save()) {
				throw new Exception(strip_tags(Html::errorSummary($holder)), 500);
			}
		}
		return $holder;
	}
}
