<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiErv;

use common\components\ApiModule;
use common\models\Api;
use common\models\Currency;
use common\models\Orders;
use common\modules\ApiErv\components\ProgramSearch;
use common\modules\ApiErv\models\AdditionalCondition;
use common\modules\ApiErv\models\Program;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\forms\TravelForm;
use Yii;
use yii\base\Exception;
use yii\web\HttpException;

/**
 * Class Module Модуль страхования ЕРВ
 * @package common\modules\ApiErv
 */
class Module extends ApiModule
{
	//public $downloadPath = 'https://online.erv.ru/axis2_verifyru';
    /**
     * @var string URL загрузки
     */
    public $downloadPath = 'https://online.erv.ru/axis2';

	//public $uri = 'https://online.erv.ru/axis2_verifyru/services/insurance3.2?wsdl';
    /**
     * @var string URL АПИ
     */
    public $uri = 'https://online.erv.ru/axis2/services/insurance3.2?wsdl'; // Боевой

	//public $location = "https://online.erv.ru/axis2_verifyru/services/insurance3.2";
    /**
     * @var string URL АПИ
     */
    public $location = "https://online.erv.ru/axis2/services/insurance3.2"; // Боевой
    /**
     * @var array Данніе авторизации
     */
    public $auth = [
		'agentCode' => "E4630",
		'userName' => "E4630_ws_user",
		'userPasswd' => "ODI)(ue0jfc0w9e" // Боевой
		//'userPasswd' => "E4630_ws_user"
	];

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'common\modules\erv\controllers';
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
	    // custom initialization code goes here
    }

    /**
     * @return \SoapClient геттер клиента транспорта
     */
    public function getSoap(){
		if (!$this->_soap) {
			$this->_soap = new \SoapClient( $this->uri, array("trace" => 1, "location" => $this->location,));
		}
		return $this->_soap;
	}

    /**
     * @inheritdoc
     * @return array
     */
    public static function getAdminMenu(){
		return [
			'label'=>Yii::t('backend', 'ERV'),
			'url' => '#',
			'icon'=>'<i class="fa fa-address-book"></i>',
			'options'=>['class'=>'treeview'],
			'items'=>[
				['label'=>Yii::t('backend', 'Регионы'), 'url'=>['/erv-regions/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Страны'), 'url'=>['/erv-country/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Дополнительные условия'), 'url'=>['/erv-additional-condition/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Риски'), 'url'=>['/erv-risk/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Программы'), 'url'=>['/erv-program/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
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
	 * @return float|int
	 * @throws Exception
	 */
	public function calcPrice($program, $form, $calc_type = self::CALC_LOCAL){
		switch ($calc_type){
			case self::CALC_LOCAL:
				return $this->calcLocal($program, $form);
				break;
			case self::CALC_API:
				return $this->calcApi($program, $form);
				break;
			default:
				throw new Exception('Calculation type not implemented: '.$calc_type, 501);
		}
	}

    /**
     * Локальній расчет стоимости программы страхования
     * @param Program $program
     * @param TravelForm $form
     *
     * @return float
     */
    public function calcLocal(Program $program, TravelForm $form){
		$baseAmount = $program->price * $form->dayCount;

		$amount = $this->applyAdditionalConditions($program, $form, $baseAmount);

		return Currency::convert($amount);
	}

    /**
     * Расчет стоимости программы страхования на стороне АПИ
     * @param Program $program
     * @param TravelForm $form
     *
     * @return float
     */
    public function calcApi(Program $program, TravelForm $form){
		$order = $this->getOrder($form, $program);
		return Currency::convert($order->price, $order->currency->char_code, Currency::RUR);
	}

    /**
     * Обработка дополнительных параметров
     * @param Program $program
     * @param TravelForm $form
     * @param $baseAmount
     *
     * @return float|int|string
     */
    protected function applyAdditionalConditions(Program $program, TravelForm $form, $baseAmount){
		$amount = $baseAmount;

		if ($form->travellers) {
			$k = array_fill(0, $form->travellersCount, 1);
			foreach($form->travellers as $key => $traveller){
				$birthday = \DateTime::createFromFormat('Y-m-d', $traveller->birthday);
				if ($birthday) {
					$now = new \DateTime();
					$i = $birthday->diff($now);
					if ($i->y >= 65){
						$model = AdditionalCondition::findOne(['slug' => AdditionalCondition::CASE_OVER_65]);
						if ($model){
							$k[$key] = $model->value;
						}
					};
					if ($i->y >= 80){
						$model = AdditionalCondition::findOne(['slug' => AdditionalCondition::CASE_OVER_80]);
						if ($model){
							$k[$key] = $model->value;
						}
					};
				}
			}

			$amount = 0;
			foreach ($k as $key => $koef){
				$amount += $baseAmount * $koef;
			}
		} else {
			$amount = $form->travellersCount * $amount;
		}

		foreach($form->params as $param){
			if ($param->handler->slug == FilterParamPrototype::SLUG_SPORT && $param->handler->checked) {
				$model = AdditionalCondition::findOne(['slug' => AdditionalCondition::CASE_RISKFUL_SPORT]);
				if ($model){
					$amount = $amount * $model->value;
				}
			}

			if ($param->handler->slug == FilterParamPrototype::SLUG_CANCEL && $param->handler->checked) {
				$cancel_amount = $param->handler->variant['amount'];
				$sick_list = $param->handler->variant['sick-list'];
				$condition_code = ($sick_list)?'CANCELLATION_PLUS':'CANCELLATION';
				$model = AdditionalCondition::findOne(['slug' => $condition_code]);
				if ($model){
					$amount = $amount + $form->travellersCount*(($cancel_amount*$model->value)/100);
				}
			}
		}

		return $amount;
	}

    /**
     * @inheritdoc
     * @param $program_id
     *
     * @return Program
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
		$reply = $this->soap->userLogin($this->auth);
		$userUniqueId = $reply->return->userUniqueId;
		$activatePolicy = [
			'uniqueId' => $userUniqueId,
			'policyNumber' => $order->info->policyNumber,
			"currency" => "EUR",
		];

		$reply = $this->soap->activatePolicy($activatePolicy);

		$order_info = $order->info;
		$order_info->request_pay = $activatePolicy;
		$order_info->responce_pay = $reply;
		$order->info = $order_info;

		if ($reply->return->error->errCode == 0) {
			$res = true;
			$order->status = Orders::STATUS_PAYED_API;
		}

		if (!$order->save()) Yii::error($this->name." confirmApiPayment error".print_r($order->getErrors(), true));

		return $res;
	}

	public function sendOrderMail($order)
	{
		if ($policy_url = $this->getPoliceLink($order)) {

			$policy_holder = $order->calc_form->payer;

			$body = \Yii::$app->controller->renderFile('@common/modules/ApiErv/views/email/order.php', [
				'site' => getenv('FRONTEND_URL'),
				'name' => $policy_holder->first_name,
				'policy' => $policy_url,
				'rule' => $order->program->rule_base_url."/".$order->program->rule_path
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
     *
     * @return array
     * @throws HttpException
     */
    public function downloadOrder(Orders $order, $additionalInfo=null){
		if (!$additionalInfo) {
			$reply = $this->soap->userLogin($this->auth);
			$additionalInfo = $reply->return->userUniqueId;
		}

		$log = [];
		if ($order->status == Orders::STATUS_PAYED_API) {
			$log[time()] = 'Старт загрузки информации из апи';
			$getPolicyAgreement = [
				'uniqueId' => $additionalInfo,
				'policyNumber' => $order->info->policyNumber,
				'language' => 'ru'
			];
			$reply = $this->soap->getPolicyAgreement($getPolicyAgreement);

			$log[time()] = 'Получен ответ: <pre>' . print_r($reply->return, true) . '</pre>';

			if ($reply->return->error->errCode == 0) {
				$log[time()] = 'Сохранение полиса из ' . $this->downloadPath . $reply->return->documentInfo->location;
				$url = $this->downloadPath . $reply->return->documentInfo->location;
				$this->wgetPolice($order, $url);
			} else {
				throw new HttpException(500, $reply->return->error->errInfo);
			}
			$log[time() + 1] = 'Завершено';

			$order->is_police_downloaded = 1;
			if (!$order->save()) Yii::error($this->name." downloadOrder error".print_r($order->getErrors(), true));
		}
		return $log;
	}

	/**
     * @inheritdoc
	 * @param TravelForm $form
	 * @param $program
	 *
	 * @return false|Orders
	 */
	public function getOrder(TravelForm $form, $program_id){
		$program = Program::findOne(['id' => $program_id]);
		/** @var Program $program */

		$cancellationAmount = 0;
		$supplementaries_tariffs = [];
		foreach($form->params as $param){
			if ($param->handler->slug == FilterParamPrototype::SLUG_SPORT && $param->handler->checked) {
				if ($program->tariff_code_sport && $program->tariff_code_sport!='') {
					$supplementaries_tariffs[] = $program->tariff_code_sport;
				}
			}

			if ($param->handler->slug == FilterParamPrototype::SLUG_CANCEL && $param->handler->checked) {
				$cancellationAmount = $param->handler->variant['amount'];
				$sick_list = $param->handler->variant['sick-list'];
				if ($sick_list==1) {
					if ($program->tariff_code_cancel_p && $program->tariff_code_cancel_p!='') {
						$supplementaries_tariffs[] = $program->tariff_code_cancel_p;
					}
				} else {
					if ($program->tariff_code_cancel && $program->tariff_code_cancel!='') {
						$supplementaries_tariffs[] = $program->tariff_code_cancel;
					}
				}
			}
		}

		$polParams = array();
		$ccodes = [];
		$shengen_country = false;
		$finland = false;
		foreach ($form->countriesModels as $model) {
			$ccodes[] = $model->iso_alpha3;
			if ($model->shengen==1) $shengen_country=true;
			if ($model->iso_alpha3=='FIN') $finland=true;
		}

		$destination = implode(';', $ccodes);

		$polParams["destination"] = $destination;
		$polParams["insureds"] = [];

		foreach($form->travellers as $traveller){
			$info = [
				"birthDate" => $traveller->birthday,
				"cancellationAmount" => $cancellationAmount,
				"currency" => "EUR",
				"name" => $traveller->first_name,
				"surname" => $traveller->last_name,
				"tariff" => $program->tariff_code,
				"supplementaries" => $supplementaries_tariffs
			];
			$polParams['insureds'][] = $info;
		}

		$polParams["policyHolder"] = array(
			"birthDate" => $form->payer->birthday,
			"email" => $form->payer->email,
			"name" => $form->payer->first_name,
			"surname" => $form->payer->last_name,
			"person" => true,
			"phone" => $form->payer->phone,
			"pin" => $form->payer->passport_no.' '.$form->payer->passport_seria,
		);
		$polParams["organized"] = false;
		$polParams["productCode"] = $program->product_code; // PRODUCT
		$polParams["regionCode"] = $program->region->code;
		$polParams["issuedOn"] = date("Y-m-d");


		$from = \DateTime::createFromFormat('d.m.Y', trim($form->dateFrom));
		$to   = \DateTime::createFromFormat('d.m.Y', trim($form->dateTo));

		if ($shengen_country) {
			$to->add(new \DateInterval('P15D'));
		}

		if ($finland) {
			$from = new \DateTime('tomorrow');
		}

		$polParams["startDay"] = $from->format('Y-m-d');
		$polParams["lastDay"]  = $to->format('Y-m-d');
		$polParams["totalDays"] = $form->dayCount;
		$polParams["transport"] = "O";

		$reply = $this->soap->userLogin($this->auth);
		$userUniqueId = $reply->return->userUniqueId;

		$createPolicy = [
			'uniqueId' => $userUniqueId,
			'policyParms' => $polParams,
			"activate" => false,
			"currency" => "EUR",
		];
		$reply = $this->soap->createPolicy($createPolicy);

		if ($reply->return->error->errCode == 0){
			$order = new Orders();
			$order->api_id      = $this->model->id;
			$order->price       = $reply->return->price;
			$order->currency_id = Currency::findOne(['char_code' => $reply->return->currency])->id;
			$order->status      = Orders::STATUS_NEW;
			$order->holder_id   = $this->getHolder($form->payer)->id;
			$order->info        = $reply->return;
			$order->calc_form   = $form;
			$order->program     = $program;
			if (!$order->save()) {
				var_dump($order->errors);
				die();
			}

			return $order;
		} else {
			throw new HttpException(500, $reply->return->error->errInfo);
/*
			var_dump($info);
			var_dump($reply->return->error);
			die();
			return false;
*/
		}

	}
}
