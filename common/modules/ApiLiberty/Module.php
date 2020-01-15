<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty;

use common\components\ApiModule;
use common\models\Currency;
use common\models\Orders;
use common\modules\ApiLiberty\components\ProgramSearch;
use common\components\Calculator\forms\TravelForm;
use common\modules\ApiLiberty\models\Product;
use common\modules\ApiLiberty\models\Program;
use Yii;
use yii\base\Exception;
use yii\base\Object;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\httpclient\Client;

/**
 * Class Module Модуль страховой Либерти
 * @package common\modules\ApiLiberty
 */
class Module extends ApiModule
{
    /**
     * @inheritdoc
     */

	public $api_url = 'https://liberty24.ru/services/vzwidget/';
    /**
     * @var string Параметр аутентификации
     */
    public $pin = '1x677ZwE1ReJ24SPC802';
	//public $pin = 45654;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    /**
     * Загрузка продуктов из АПИ
     * @return array
     * @throws Exception
     */
    public function getProducts() {
        $client = new Client();

        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl($this->api_url.'products')
            ->setData(['pin' => $this->pin])
            ->send();
        if ($response->isOk) {
            $products = [];
            if (isset($response->data['travelProductSimple'])) {
                $products = $response->data['travelProductSimple'];
            }
            return $products;
        } throw new Exception("Error retrieving products", 500);
    }

    /**
     * Загрузка территорий из АПИ
     * @param $productId
     *
     * @return array
     * @throws Exception
     */
    public function getTerritories($productId) {
        $client = new Client();

        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl($this->api_url.'get_territories')
            ->setData(['pin' => $this->pin, 'productId' => $productId])
            ->send();
        if ($response->isOk) {
            $territories = [];
            if (isset($response->data['vzArea'])) {
				$territories = $response->data['vzArea'];
            }
            return $territories;
        } throw new Exception("Error retrieving territories for product ".$productId, 500);
    }

    /**
     * Загрузка опций из АПИ
     * @param $productId
     *
     * @return array
     * @throws Exception
     */
    public function getOccupations($productId) {
		$client = new Client();

		$response = $client->createRequest()
			->setMethod('get')
			->setUrl($this->api_url.'get_occupations')
			->setData(['pin' => $this->pin, 'productId' => $productId])
			->send();
		if ($response->isOk) {
			$occupations = [];
			if (isset($response->data['travelOccupationTO'])) {
				$occupations = $response->data['travelOccupationTO'];
			}
			return $occupations;
		} throw new Exception("Error retrieving occupations for product ".$productId, 500);
	}

    /**
     * Загрузка рисков из АПИ
     * @param $productId
     *
     * @return array
     * @throws Exception
     */
    public function getRisks($productId) {
		$client = new Client();

		$response = $client->createRequest()
			->setMethod('get')
			->setUrl($this->api_url.'get_risks')
			->setData(['pin' => $this->pin, 'productId' => $productId])
			->send();
		if ($response->isOk) {
			$risks = [];
			if (isset($response->data['riskSimple'])) {
				$risks = $response->data['riskSimple'];
			}
			return $risks;
		} throw new Exception("Error retrieving occupations for product ".$productId, 500);
	}

    /**
     * Загрузка аттрибутов рисков из АПИ
     * @param $productId
     * @param $riskId
     * @param $countryId
     * @param $currencyId
     *
     * @return array
     * @throws Exception
     */
    public function getRiskSS($productId, $riskId, $countryId, $currencyId) {
		$client = new Client();

		$response = $client->createRequest()
			->setMethod('get')
			->setUrl($this->api_url.'get_ss_risk')
			->setData(['pin' => $this->pin, 'productId' => $productId, 'riskId' => $riskId, 'countryId' => $countryId, 'currencyId' => $currencyId])
			->send();
		if ($response->isOk) {
			$summs = [];
			if (isset($response->data['insuredSummSimple'])) {
				$summs = $response->data['insuredSummSimple'];
			}
			return $summs;
		} throw new Exception("Error retrieving ss", 500);
	}

    /**
     * Тестовый метод
     * @return mixed
     */
    public function testOrder() {

		$order_data = [
			'Vz_FullCalcRQ'=>[
				"pin"=> $this->pin,
				"productId"=> 14075,
				"startDate"=> "2017-10-04 00:00:00",
				"endDate"=> "2017-10-05 00:00:00",
				"number_of_days"=> 1,
				"insured_area"=> [
					"id_area"=> 72,
					"name"=> "Финляндия"
				],
				"medical_option"=> 0,
				"number_of_lugg"=> 0,
				"Risks"=> [
					[
						"id_risk"=> 346,
						"insuredSum"=> [
							"currency_id"=> 14,
							"summ"=> 50000
						]
					]
				],
				"insuredPersons"=> [
					[
						"family"=> "fio",
						"name"=> "ivan",
						"birhDate"=> "1999-01-01 00:00:00",
						"passportSeria"=> "23",
						"passportNumber"=> "45566"
					]
				]
			]
		];

		$client = new Client([
			'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
		]);

		$response = $client->createRequest()
			->setFormat(Client::FORMAT_JSON)
			->setMethod('post')
			->setUrl($this->api_url.'calc/')
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
			'label'=>Yii::t('backend', 'Liberty'),
			'url' => '#',
			'icon'=>'<i class="fa fa-address-book"></i>',
			'options'=>['class'=>'treeview'],
			'items'=>[
				['label'=>Yii::t('backend', 'Продукты'), 'url'=>['/liberty-product/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Опции по медицине'), 'url'=>['/liberty-occupation/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Риски'), 'url'=>['/liberty-risk/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
                ['label'=>Yii::t('backend', 'Территории'), 'url'=>['/liberty-territory/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
                ['label'=>Yii::t('backend', 'Валюты'), 'url'=>['/liberty-currency/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
			]
		];
	}

    /**
     * @inheritdoc
     * @param TravelForm $form
     *
     * @return \common\models\ProgramResult|null
     */
    public function search( TravelForm $form ){
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
	 * @return float|int|Orders
	 * @throws Exception
	 */
	public function calcPrice($program, $form, $calc_type = self::CALC_LOCAL){
		switch ($calc_type){
			case self::CALC_LOCAL:
				return $this->getPrice($form, $program);
				break;
			case self::CALC_API:
				$order = $this->getOrder($form, $program->id);
				return $order->price;
				break;
			default:
				throw new Exception('Calculation type not implemented: '.$calc_type, 501);
		}
	}


    /**
     * @inheritdoc
     * @param $program_id
     *
     * @return Program
     */
    public function getProgram($program_id) {
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
			'Vz_BookRQ'=>[
				"pin" => $this->pin,
				"calculation_Id" => $responce['Vz_CalcRS']['calcualtion_id'],
				"insurerIN" =>[
					"family" => $order->holder->last_name,
					"name" => $order->holder->first_name,
					"birthDate" => $order->holder->birthdayAsDate('Y-m-d 00:00:00'),
					"e_mail" => $order->holder->email,
					"phone" => preg_replace('~\D+~','',$order->holder->phone)
				]
			]
		];

		$order_info = $order->info;
		$order_info['request_pay'] = $order_request;

		try {
			$client = new Client([
				'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
			]);
			$response = $client->createRequest()
				->setFormat(Client::FORMAT_JSON)
				->setMethod('post')
				->setUrl($this->api_url . 'book')
				->setData($order_request)
				->setOptions([
					CURLOPT_CONNECTTIMEOUT => 10, // connection timeout
					CURLOPT_TIMEOUT => 50, // data receiving timeout
				])
				->send();

			$order_info['responce_pay'] = $response->data;

			if ($response->isOk && isset($response->data['Vz_BookRS']['result']) && $response->data['Vz_BookRS']['result']) {
				if ($response->data['Vz_BookRS']['result']) {
					$res = true;
					$order->status = Orders::STATUS_PAYED_API;
				}
			}
		}catch (Exception $e) {

		}

		$order->info = $order_info;
		if (!$order->save()) Yii::error($this->name." confirmApiPayment error".print_r($order->getErrors(), true));

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
		if ($order->status == Orders::STATUS_PAYED_API) {
			$responce = $order->info['responce_pay'];

			if (isset($responce['Vz_BookRS']['result']) && $responce['Vz_BookRS']['result'] && isset($responce['Vz_BookRS']['policy_link'])) {
				$log[time()] = 'Старт загрузки информации из апи';

				$url = $responce['Vz_BookRS']['policy_link'];

				$log[time()] = 'Сохранение полиса из ' . $url;
				$this->wgetPolice($order, $url);

				$log[time() + 1] = 'Завершено';

				$order->is_police_downloaded = 1;
				if (!$order->save()) Yii::error($this->name." downloadOrder error".print_r($order->getErrors(), true));
			}
		}
		return $log;
	}

	/**
     * Получение стоимости из АПИ
	 * @param TravelForm $form
	 * @param Program $program_id
	 *
	 * @return false|Orders
	 */
	public function getPrice($form, Program $program) {
		$risks = [];
		$risks[] = [
			"id_risk"=> $program->riskId,
			"insuredSum"=> [
				"currency_id"=> 14,
				"summ"=> $program->amount
			]
		];

		foreach ($program->risks as $risk_id=>$one_risk) {
			$risks[] = [
				"id_risk"=> $risk_id,
				"insuredSum"=> [
					"currency_id"=> 14,
					"summ"=> $one_risk['amount']
				]
			];
		}

		$areas = [];
		foreach ($program->countries as $id_area=>$name) {
			$areas[] = [
				"id_area"=> $id_area,
				"name"=> $name
			];
		}

		$persons = [];
		if (count($form->travellers)==$form->travellersCount) {
			foreach($form->travellers as $traveller){
				$persons[] = [
					"birhDate"=> $traveller->birthdayAsDate('Y-m-d 00:00:00')
				];
			}
		} else {
			for ($i = 1; $i <= $form->travellersCount; $i++) {
				$persons[] = [
					"birhDate" => "1999-01-01 00:00:00",
				];
			}
		}

		$order_data = [
			'Vz_FullCalcRQ'=>[
				"pin"=> $this->pin,
				"productId"=> $program->productId,
				"startDate"=> \DateTime::createFromFormat('d.m.Y', trim($form->dateFrom))->format('Y-m-d 00:00:00'),
				"endDate"=> \DateTime::createFromFormat('d.m.Y', trim($form->dateTo))->format('Y-m-d 00:00:00'),
				"number_of_days"=> $form->dayCount,
				"insured_area"=> $areas,
				"medical_option"=> $program->medical_option,
				"number_of_lugg"=> $form->travellersCount,
				"Risks"=> $risks,
				"insuredPersons"=> $persons
			]
		];

		try {
			$client = new Client([
				'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
			]);
			$response = $client->createRequest()
				->setFormat(Client::FORMAT_JSON)
				->setMethod('post')
				->setUrl($this->api_url . 'calc')
				->setData($order_data)
				->setOptions([
					CURLOPT_CONNECTTIMEOUT => 10, // connection timeout
					CURLOPT_TIMEOUT => 50, // data receiving timeout
				])
				->send();

			if ($response->isOk) {
				if (isset($response->data['Vz_CalcRS']['insured_premium']['summ'])) {
					$currency_from = ($response->data['Vz_CalcRS']['insured_premium']['currency'] != 'RUB') ? $response->data['Vz_CalcRS']['insured_premium']['currency'] : Currency::RUR;
					return Currency::convert($response->data['Vz_CalcRS']['insured_premium']['summ'], $currency_from, Currency::RUR);
				} else return 0;
			} else return 0;
		}catch (Exception $e) {
			return 0;
		}
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

		$risks = [];
		$risks[] = [
			"id_risk"=> $program->riskId,
			"insuredSum"=> [
				"currency_id"=> 14,
				"summ"=> $program->amount
			]
		];

		foreach ($program->risks as $risk_id=>$one_risk) {
			$risks[] = [
				"id_risk"=> $risk_id,
				"insuredSum"=> [
					"currency_id"=> 14,
					"summ"=> $one_risk['amount']
				]
			];
		}

		$areas = [];
		foreach ($program->countries as $id_area=>$name) {
			$areas[] = [
				"id_area"=> $id_area,
				"name"=> $name
			];
		}

		$persons = [];
		foreach($form->travellers as $traveller){
			$persons[] = [
				"family"=> $traveller->last_name,
				"name"=> $traveller->first_name,
				"birhDate"=> $traveller->birthdayAsDate('Y-m-d 00:00:00')
			];
		}


		$order_data = [
			'Vz_FullCalcRQ'=>[
				"pin"=> $this->pin,
				"productId"=> $program->productId,
				"startDate"=> \DateTime::createFromFormat('d.m.Y', trim($form->dateFrom))->format('Y-m-d 00:00:00'),
				"endDate"=> \DateTime::createFromFormat('d.m.Y', trim($form->dateTo))->format('Y-m-d 00:00:00'),
				"number_of_days"=> $form->dayCount,
				"insured_area"=> $areas,
				"medical_option"=> $program->medical_option,
				"number_of_lugg"=> $form->travellersCount,
				"Risks"=> $risks,
				"insuredPersons"=> $persons
			]
		];

		try {
			$client = new Client([
				'transport' => 'yii\httpclient\CurlTransport' // only cURL supports the options we need
			]);
			$response = $client->createRequest()
				->setFormat(Client::FORMAT_JSON)
				->setMethod('post')
				->setUrl($this->api_url . 'calc')
				->setData($order_data)
				->setOptions([
					CURLOPT_CONNECTTIMEOUT => 10, // connection timeout
					CURLOPT_TIMEOUT => 50, // data receiving timeout
				])
				->send();

			if ($response->isOk) {
				if (isset($response->data['Vz_CalcRS']['insured_premium']['summ'])) {
					$currency_from = ($response->data['Vz_CalcRS']['insured_premium']['currency'] != 'RUB') ? $response->data['Vz_CalcRS']['insured_premium']['currency'] : Currency::RUR;

					$order = new Orders();
					$order->api_id      = $this->model->id;
					$order->price       = Currency::convert($response->data['Vz_CalcRS']['insured_premium']['summ'], $currency_from, Currency::RUR);
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

					$program->in_order = 1;
					if (!$program->save()) {
						throw new Exception(strip_tags(Html::errorSummary($program)), 500);
					}
					return $order;

				} else throw new HttpException(500, 'Error retrieving result: '.$response->data);
			} else throw new HttpException(500, 'Error sending request');
		} catch (Exception $e) {
			throw new HttpException(500, $e->getMessage());
		}
	}
}
