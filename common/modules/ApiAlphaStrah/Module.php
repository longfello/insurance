<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah;

use common\components\ApiModule;
use common\models\Currency;
use common\models\Orders;
use common\models\AdditionalCondition as CommonCondition;
use common\modules\ApiAlphaStrah\components\ProgramSearch;
use common\modules\ApiAlphaStrah\models\AdditionalCondition;
use common\modules\ApiAlphaStrah\models\AdditionalConditions;
use common\modules\ApiAlphaStrah\models\Country;
use common\modules\ApiAlphaStrah\models\Price;
use common\modules\ApiAlphaStrah\models\StruhSum;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\forms\TravelForm;
use Yii;
use yii\base\Exception;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\HttpException;

/**
 * Class Module АПИ АльфаСтрахование
 * @package common\modules\ApiAlphaStrah
 */
class Module extends ApiModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'common\modules\ApiAlphaStrah\controllers';

    /**
     * @inheritdoc
     */
    public $uri = 'https://ti.alfastrah.ru/TIService/InsuranceAlfaService.svc?wsdl';
    /**
     * @var string идентификатор агента
     */
    public $agentUid = '5AD083FC-F331-49AA-8960-94AF74826185';
    /**
     * @var string имя пользователя
     */
    public $login = 'bu1_bulo';
    /**
     * @var string пароль
     */
    public $password = '03eP1WLs';

    /**
     * @return \SoapClient геттер клиента
     */
    public function getSoap(){
		if (!$this->_soap) {
			$this->_soap = new \SoapClient( $this->uri );
		}
		return $this->_soap;
	}

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    /**
     * @inheritdoc
     */
    public static function getAdminMenu(){
		return [
			'label'=>Yii::t('backend', 'Альфа Страхование'),
			'url' => '#',
			'icon'=>'<i class="fa fa-address-book"></i>',
			'options'=>['class'=>'treeview'],
			'items'=>[
				['label'=>Yii::t('backend', 'Страны'), 'url'=>['/as-country/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Програмы страхования'), 'url'=>['/as-insurance-programm/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Доп.условия (спорт)'), 'url'=>['/as-additional-condition/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Доп.условия'), 'url'=>['/as-additional-conditions/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Риски'), 'url'=>['/as-risk/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Страховые суммы (программы)'), 'url'=>['/as-amount/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Страховые суммы (риски)'), 'url'=>['/as-struh-sum/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Ассистенты'), 'url'=>['/as-assistance/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Валюты'), 'url'=>['/as-currency/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Регионы'), 'url'=>['/as-regions/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
			]
		];
	}

    /**
     * @inheritdoc
     * @param TravelForm $form
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
	 * @param Price $price
	 * @param TravelForm $form
	 * @param string $calc_type
	 *
	 * @return float|int
	 * @throws Exception
	 */
	public function calcPrice($price, $form, $calc_type = self::CALC_LOCAL){
		switch ($calc_type){
			case self::CALC_LOCAL:
				return $this->calcLocal($price, $form);
				break;
			case self::CALC_API:
				return $this->calcApi($price, $form);
				break;
			default:
				throw new Exception('Calculation type not implemented: '.$calc_type, 501);
		}
	}

    /**
     * Локальный расчет стоимости программы страхования
     * @param Price $price
     * @param TravelForm $form
     *
     * @return float
     */
    public function calcLocal(Price $price, TravelForm $form){
		$baseAmount = $price->price * $form->dayCount;
		$amount = $this->applyAdditionalConditions($price, $form, $baseAmount);
		return Currency::convert($amount);
	}

    /**
     * Расчет стоимости программы страхования на стороне API
     * @param Price $price
     * @param TravelForm $form
     *
     * @return float
     */
    public function calcApi(Price $price, TravelForm $form){

//		throw new Exception("Not ready yet", 500);
		$order = $this->getOrder($form, $price->id);
		return Currency::convert($order->price, $order->currency->char_code, Currency::RUR);
	}

    /**
     * Расчет стоимости дополнительных условий
     * @param Price $price
     * @param TravelForm $form
     * @param $baseAmount
     *
     * @return float|int|mixed
     * @throws Exception
     */
    protected function applyAdditionalConditions(Price $price, TravelForm $form, $baseAmount){
		$koef = 1;

		$conditions = AdditionalConditions::find()->all();
		foreach($conditions as $condition){
			/** @var $condition \common\modules\ApiAlphaStrah\models\AdditionalConditions*/
			$modelClass = $condition->class;
			if (class_exists($modelClass)){
				$model = new $modelClass([
					'form'   => $form,
					'params' => $condition->params,
					'baseAmount' => $baseAmount
				]);
				/** @var $model \common\modules\ApiAlphaStrah\components\AdditionalConditionPrototype */
				$koef *= $model->getKoef();
			}
		}
		$amount = $form->travellersCount * $baseAmount * $koef;

		foreach($form->params as $param){
			if ($param->handler->slug == FilterParamPrototype::SLUG_CANCEL && $param->handler->checked) {
				$cancel_amount = $param->handler->variant['amount'];
				//$country = Country::findOne(['parent_id' => $form->countries]);
				//$country = Country::find()->alias('c')->InnerJoin('api_alpha_country2dict c2d', 'c2d.api_id = c.countryID')
				//	->where(['c2d.internal_id' => $form->countries])->andWhere(['c.enabled'=>1])->one();
				$visa_required = false;
				$countries= Country::find()->alias('c')->InnerJoin('api_alpha_country2dict c2d', 'c2d.api_id = c.countryID')
					->where(['c2d.internal_id' => $form->countries])->andWhere(['c.enabled'=>1])->andWhere(['c.region_id'=>$price->region_id])->all();
				foreach ($countries as $country) {
					/** @var $country Country */
					if (!$visa_required && $country->visa) $visa_required=true;
				}

				if ($countries) {
					$percent = 0;
					if ($cancel_amount<=1500) {
						$percent = ($visa_required)?0.04:0.03;
					} elseif ($cancel_amount>1500 && $cancel_amount<=2500) {
						$percent = ($visa_required)?0.045:0.035;
					} elseif ($cancel_amount>2500 && $cancel_amount<=4000) {
						$percent = ($visa_required)?0.055:0.045;
					}
					$amount += $cancel_amount * $percent * $form->travellersCount;
				}
			}
			if ($param->handler->slug == FilterParamPrototype::SLUG_ACCIDENT && $param->handler->checked) {
				if ($price->accident_sum_id === Price::SUM_INCLUDED){
					// Risk included into programm
				} elseif ($price->accident_sum) {
					$amount += $price->accident_sum->premia * $form->dayCount;
				} else {
					throw new Exception("Not included and not selected risk.");
				}
			}
			if ($param->handler->slug == FilterParamPrototype::SLUG_CIVIL && $param->handler->checked) {
				if ($price->civil_sum_id === Price::SUM_INCLUDED){
					// Risk included into programm
				} elseif ($price->civil_sum) {
					$amount += $price->civil_sum->premia * $form->dayCount;
				} else {
					throw new Exception("Not included and not selected risk.");
				}
			}
			if ($param->handler->slug == FilterParamPrototype::SLUG_LUGGAGE && $param->handler->checked) {
				if ($price->luggage_sum_id === Price::SUM_INCLUDED){
					// Risk included into programm
				} elseif ($price->luggage_sum) {
					$amount += $price->luggage_sum->premia;
				} else {
					throw new Exception("Not included and not selected risk.");
				}
			}
		}
		return $amount;
	}

    /**
     * @inheritdoc
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
		$request = $order->info['request'];
		$request['policy']['common']['operation'] = 'Create';
		$responce = $this->getSoap()->NewPolicty($request);

		$order_info = $order->info;
		$order_info['request_pay'] = $request;
		$order_info['responce_pay'] = $responce;
		$order->info = $order_info;

		try {
			$data = (array)$responce;
			$data = array_pop($data);

			if (isset($data->common) && isset($data->common->policyID)) {
				$res = true;
				$order->status = Orders::STATUS_PAYED_API;
			}
		} catch (Exception $e) {

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

			$body = \Yii::$app->controller->renderFile('@common/modules/ApiAlphaStrah/views/email/order.php', [
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
     *
     * @return array
     */
    public function downloadOrder(Orders $order, $additionalInfo=null){
		$log = [];
		if ($order->status == Orders::STATUS_PAYED_API) {
			if (isset($order->info['responce_pay'])) {
				$log[time()] = 'Старт загрузки информации из апи';
				$responce = $order->info['responce_pay'];
				$data = (array)$responce;
				$data = array_pop($data);

				$url = $data->common->policyLink;

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
     * @inheritdoc
	 * @param TravelForm $form
	 * @param $program
	 *
	 * @return false|Orders
	 */
	public function getOrder(TravelForm $form, $program_id){
		$price = Price::findOne(['id' => $program_id]);
		/** @var Price $price */

		$payer_model = $this->getHolder($form->payer);

		//$country = Country::findOne(['parent_id' => $form->countries]);
		$visa_required = false;
		$countries= Country::find()->alias('c')->InnerJoin('api_alpha_country2dict c2d', 'c2d.api_id = c.countryID')
			->where(['c2d.internal_id' => $form->countries])->andWhere(['c.enabled'=>1])->andWhere(['c.region_id'=>$price->region_id])->all();
		$countryUIDs = array();
		foreach ($countries as $country) {
			/** @var $country Country */
			if (!$visa_required && $country->visa) $visa_required=true;
			$countryUIDs[] = $country->countryUID;
		}

		$from = \DateTime::createFromFormat('d.m.Y', trim($form->dateFrom));
		$to   = \DateTime::createFromFormat('d.m.Y', trim($form->dateTo));

		$startDay = $from->format('Y-m-d');
		$lastDay  = $to->format('Y-m-d');

		$insureds = [];
		foreach($form->travellers as $traveller){
			$insureds[] = [
				'dateOfBirth' => $traveller->birthdayAsDate('Y-m-d'),
				'fio'         => $traveller->first_name.' '.$traveller->last_name
			];
		}

		$request_risk = [
			[
				'riskUID' => $price->struh_sum->riskUID,
				'amountAtRisk' => (string)$price->struh_sum->strahSummFrom,
				'amountCurrency' => $price->struh_sum->valutaCode,
				'franshize' => 0,
			]
		];
		$addSport = false;
		foreach($form->params as $param){
			if ($param->handler->slug == FilterParamPrototype::SLUG_SPORT && $param->handler->checked) {
				$addSport = true;
			}
			if ($param->handler->slug == FilterParamPrototype::SLUG_ACCIDENT && $param->handler->checked) {
				if ($price->accident_sum){
					$request_risk[] = [
						'riskUID' => $price->accident_sum->riskUID,
						'amountAtRisk' => $price->accident_sum->strahSummFrom,
						'amountCurrency' => 'EUR',
						'riskVariantUID' => $price->accident_sum->variantUid,
					];
				}
			}
			if ($param->handler->slug == FilterParamPrototype::SLUG_LUGGAGE && $param->handler->checked) {
				if ($price->luggage_sum){
					$request_risk[] = [
						'riskUID' => $price->luggage_sum->riskUID,
						'amountAtRisk' => $price->luggage_sum->strahSummFrom,
						'amountCurrency' => 'EUR',
						'riskVariantUID' => $price->luggage_sum->variantUid,
					];
				}
			}
			if ($param->handler->slug == FilterParamPrototype::SLUG_CIVIL && $param->handler->checked) {
				if ($price->civil_sum){
					$request_risk[] = [
						'riskUID' => $price->civil_sum->riskUID,
						'amountAtRisk' => $price->civil_sum->strahSummFrom,
						'amountCurrency' => 'EUR',
						'riskVariantUID' => $price->civil_sum->variantUid,
					];
				}
			}

			if ($param->handler->slug == FilterParamPrototype::SLUG_CANCEL && $param->handler->checked) {
				$cancellationAmount = $param->handler->variant['amount'];

				if ($visa_required) {
					$visa = '(визовые страны)';
				} else {
					$visa = '(безвизовые страны)';
				}

				$risk_variant = StruhSum::find()
					->where(['=','riskUID', 'e041e5b7-6567-4210-8702-6a29e3fef229'])
					->andWhere(['<=','strahSummFrom',$cancellationAmount])
					->andWhere(['>=','strahSummTo',$cancellationAmount])
					->andWhere(['like', 'variant', $visa])->one();

				if ($risk_variant) {
					$request_risk[] = [
						'riskUID' => 'e041e5b7-6567-4210-8702-6a29e3fef229',
						'amountAtRisk' => $cancellationAmount,
						'amountCurrency' => 'EUR',
						'riskVariantUID' => $risk_variant->variantUid,
					];
				}

			}
		}

		$request = [
			'policy' => [
				'common' => [
					'operation' => 'Draft',
					'insuranceProgrammUID' => $price->program->insuranceProgrammUID,
//					'countryUID' => $country->countryUID,
//					'dtCreated' => date('Y-m-d'),
					'fio' => $payer_model->first_name.' '.$payer_model->last_name,
					'policyPeriodFrom' => $startDay,
					'policyPeriodTill' => $lastDay,

					'userId' => $this->agentUid,
					'userLogin' => $this->login,
					'userPSW' => $this->password,
				],
				'insureds' => [
					'insured' => $insureds
				],

				'risks' => $request_risk,
				'countryUIDs' => $countryUIDs
			]
		];

		if ($addSport) {
			$sport_uid = '0e2d9f2e-f039-404f-aed1-1347a4aa953d';

			$sport_condition = CommonCondition::findOne(['slug'=>'sport']);
			/** @var $sport_condition CommonCondition */
			if ($sport_condition) {
				$api_sport = AdditionalCondition::findOne(['parent_id'=>$sport_condition->id]);
				/** @var $api_sport AdditionalCondition */
				if ($api_sport) {
					$sport_uid = $api_sport->additionalConditionUID;
				}
			}

			$request['policy']['common']['additionalCondition'] = $sport_uid;
		}

		try {
			$responce = $this->getSoap()->NewPolicty($request);
		} catch (\Exception $e) {
			throw new HttpException(500, $e->getMessage());
		}
		$data = (array) $responce;
		$data = array_pop( $data );

		$order = new Orders();
		$order->api_id      = $this->model->id;
		$order->price       = $data->common->premRUR;
		$order->currency_id = Currency::findOne(['char_code' => Currency::RUR])->id;
		$order->status      = Orders::STATUS_NEW;
		$order->holder_id   = $payer_model->id;
		$order->info        = [
			'request' => $request,
			'responce' => $responce,
		];
		$order->calc_form   = $form;
		$order->program     = $price;

		if (!$order->save()) {
			throw new Exception(strip_tags(Html::errorSummary($order)), 500);
		}

		return $order;
	}

    /**
     * @param string $method
     * @param array $params
     *
     * @return array|mixed
     */public function request( $method, $params = [] ) {
		$parameters = array_merge( [
			'agentUid' => $this->agentUid
		], $params );
		$result     = $this->soap->$method( [ 'parameters' => $parameters ] );

		$result = (array) $result;
		$result = array_pop( $result );
		$result = (array) $result;
		$result = array_pop( $result );

		return $result;
	}


    /**
     * Получение программ из АПИ
     * @return array|mixed
     */
    public function getProgramms() {
		return $this->request( 'GetInsuranceProgramms' );
	}

    /**
     * Получение дополнительных условий из АПИ
     * @return array|mixed
     */
    public function getAdditionalConditions() {
		return $this->request( 'GetAdditionalConditions' );
	}

    /**
     * Получение вторых дополнительных условий из АПИ
     * @return array|mixed
     */
    public function getAdditionalConditions2() {
		return $this->request( 'GetAdditionalConditions2' );
	}

    /**
     * Получение стран из АПИ
     * @param null $programUid
     *
     * @return array|mixed
     */
    public function getCountries( $programUid = null ) {
		$params = [];
		if ( $programUid ) {
			$params['programUid'] = $programUid;
		}

		return $this->request( 'GetCountries', $params );
	}

    /**
     * Получение рисков из АПИ
     * @param null $programUid
     *
     * @return array|mixed
     */
    public function getRisks( $programUid = null ) {
		$params = [
			'programUid' => $programUid
		];

		return $this->request( 'GetRisks', $params );
	}

    /**
     * Получение страховых сумм из АПИ
     * @param null $countryId
     * @param null $programUid
     * @param null $riskUid
     *
     * @return array|mixed
     */
    public function getStruhSum( $countryId = null, $programUid = null, $riskUid = null ) {
		$params = [];
		if ( $countryId ) {
			$params['countryId'] = $countryId;
		}
		if ( $programUid ) {
			$params['programUid'] = $programUid;
		}
		if ( $riskUid ) {
			$params['riskUid'] = $riskUid;
		}

		return $this->request( 'GetStruhSum', $params );
	}

    /**
     * Получение франшиз из АПИ
     * @param null $countryId
     * @param null $programUid
     * @param null $riskUid
     *
     * @return array|mixed
     */
    public function getFranshize( $countryId = null, $programUid = null, $riskUid = null ) {
		$params = [];
		if ( $countryId ) {
			$params['countryId'] = $countryId;
		}
		if ( $programUid ) {
			$params['programUid'] = $programUid;
		}
		if ( $riskUid ) {
			$params['riskUid'] = $riskUid;
		}

		return $this->request( 'GetFransize', $params );
	}

    /**
     * Получение ассистентов из АПИ
     * @param null $assistanceUID
     *
     * @return array|mixed
     */
    public function getAssistance( $assistanceUID = null ) {
		$params = [];
		if ( $assistanceUID ) {
			$params['assistanceUID'] = $assistanceUID;
		}
		$result = $this->request( 'GetAssistance', $params );

		return is_array($result)?$result:array($result);
	}

    /**
     * Получение валют из АПИ
     * @return array|mixed
     */
    public function getCurrency() {
		return $this->request( 'GetCurrency');
	}

    /**
     * Получение территорий из АПИ
     * @return array|mixed
     */
    public function getTerritory() {
		return $this->request( 'GetTerritory');
	}

    /**
     * Получение расчета полиса из АПИ
     * @param $data
     *
     * @return mixed
     */
    public function calcPolice($data) {
		$result     = $this->soap->NewPolicty($data);
		return $result;
	}

}
