<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb;

use common\components\ApiModule;
use common\models\Api;
use common\models\Currency;
use common\models\Orders;
use common\models\Person;
use frontend\models\PersonInfo;
use common\modules\ApiVtb\components\riskHandlers\prototype;
use common\modules\ApiVtb\models\Country2dict;
use common\modules\ApiVtb\models\Price;
use common\modules\ApiVtb\models\Risk;
use common\modules\ApiVtb\models\Risk2internal;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\forms\TravelForm;
use Yii;
use yii\base\Exception;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * alpha module definition class
 */
class Module extends ApiModule
{

	/**
	 * @var array окружения
	 */
	public $environments = [
		self::ENV_TEST => [
			'uri' => 'https://testpartner.vtbins.ru/assets/system/os/components/soap_services/vtb/services.php?wsdl',
			'location'  => 'https://testpartner.vtbins.ru/assets/system/os/components/soap_services/vtb/services.php',
			'login' => 'int_bullosafe',
			'password' => 'int_bullosafe1978'
		],
		self::ENV_PROD => [
			'uri' => 'https://partner.vtbins.ru/assets/system/os/components/soap_services/vtb/services.php?wsdl',
			'location'  => 'https://partner.vtbins.ru/assets/system/os/components/soap_services/vtb/services.php',
			'login' => 'bullisafe_int',
			'password' => 'Iedahh7t'
		]
	];
    /**
     * @inheritdoc
     */
	public $uri = '';
    /**
     * @var string Параметр доступа к АПИ
     */
	public $location = '';

    /**
     * @var string логин
     */
    public $login;
    /**
     * @var string пароль
     */
    public $password;
    /**
     * @var string код партнера
     */
    public $partnerCode = '123-123-456';

	/**
	 * @inheritdoc
	 */
    public $controllerNamespace = 'common\modules\vtb\controllers';

	/**
	 * @inheritdoc
	 */
	public $maxTravellersCount = 5;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->uri = $this->environments[getenv('VTB_MODE')]['uri'];
		$this->location = $this->environments[getenv('VTB_MODE')]['location'];
		$this->login = $this->environments[getenv('VTB_MODE')]['login'];
		$this->password = $this->environments[getenv('VTB_MODE')]['password'];

		parent::init();

		// custom initialization code goes here
	}
    /**
     * @return \SoapClient геттер клиента транспорта
     */
    public function getSoap(){
    	if (!$this->_soap) {
		    $this->_soap = new \SoapClient( $this->uri, [
			    'login' => $this->login, 'password' => $this->password, 'authentication' => SOAP_AUTHENTICATION_DIGEST,
			    'trace' => 1, 'exceptions' => 1, 'cache_wsdl' => WSDL_CACHE_NONE, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS]);
	    }
	    return $this->_soap;
    }

    /**
     * Обертка запроса к АПИ
     * @param string $method
     * @param array $params
     *
     * @return mixed
     */
    public function request( $method, $params = [] ) {
		$result = $this->soap->$method($params);
		$xml = simplexml_load_string($result);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		return $array;
	}

    /**
     * @inheritdoc
     */
    public static function getAdminMenu(){
		return [
			'label'=>Yii::t('backend', 'VTB'),
			'url' => '#',
			'icon'=>'<i class="fa fa-address-book"></i>',
			'options'=>['class'=>'treeview'],
			'items'=>[
				['label'=>Yii::t('backend', 'Дополнительные условия'), 'url'=>['/vtb-additional-condition/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Периоды'), 'url'=>['/vtb-period/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Програмы'), 'url'=>['/vtb-program/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Регионы'), 'url'=>['/vtb-regions/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Риски'), 'url'=>['/vtb-risk/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Страны'), 'url'=>['/vtb-country/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
				['label'=>Yii::t('backend', 'Страховые суммы'), 'url'=>['/vtb-amount/index'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
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
		$searcher = new \common\modules\ApiVtb\components\ProgramSearch([
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
     * Локальный расчет стоимости
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
     * Расчет стоимости на стороне АПИ
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
     * Применение дополнительных условий
     * @param Price $price
     * @param TravelForm $form
     * @param $baseAmount
     *
     * @return float|int
     */
    protected function applyAdditionalConditions(Price $price, TravelForm $form, $baseAmount){
		$koef = 1;

		$conditions = \common\modules\ApiVtb\models\AdditionalCondition::find()->all();
		foreach($conditions as $condition){
			/** @var $condition \common\modules\ApiVtb\models\AdditionalCondition */
			$modelClass = $condition->class;
			if (class_exists($modelClass)){
				$model = new $modelClass([
					'form'   => $form,
					'params' => $condition->params,
					'baseAmount' => $baseAmount
				]);
				/** @var $model \common\modules\ApiVtb\components\AdditionalConditionPrototype */
				$koef *= $model->getKoef();
			}
		}
		$amount = $form->travellersCount * $baseAmount * $koef;

		$processed = [];
		foreach($form->params as $param){
			if ($param->handler->checked) {
				$_links = Risk2internal::findAll(['internal_id' => $param->risk_id]);
				foreach ($_links as $link) {
					$apiRisk = $link->risk;
					/** @var $apiRisk Risk */

					if (!in_array($apiRisk->id, $processed)) {
						array_push($processed, $apiRisk->id);
						/** @var $condition \common\modules\ApiVtb\models\AdditionalCondition */
						$modelClass = $apiRisk->class;
						if (class_exists($modelClass) && is_subclass_of($modelClass, prototype::className())) {
							$model = new $modelClass([
								'form' => $form,
								'param' => $param,
								'price' => $price
							]);

							$amount += $model->getAdditionalPrice();
						}
					}
				}
			}
		}

		/*
		foreach($form->params as $param){
			if ($param->handler->slug == FilterParamPrototype::SLUG_CANCEL && $param->handler->checked) {
				$cancel_amount = $param->handler->variant['amount'];
				$model = AdditionalCondition::findOne(['name' => 'Отмена поездки']);
				if ($model){
					$amount += ($cancel_amount*$model->params)/100;
				}
			}
		}
		*/



		return $amount;
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
	public function confirmApiPayment( Orders $order )
	{
		$res = false;
		$payer_model = $this->getHolder($order->calc_form->payer);

		$requestID = $payer_model->id.'-'.$order->program->id.'-'.time();

		$xml = '<?xml version="1.0" encoding="utf-8" ?>
			<Root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="RequestConfirm.xsd">
			<Info source="'.$this->login.'" export_date="'.date('d.m.Y H:i:s').'" export_id="'.$order->id.'" export_version="2"/>
			<ContractsCount>1</ContractsCount>
			<Contracts>
			   <Contract>
				  <RequestID>'.$requestID.'</RequestID>
				  <ContractID>'.$order->info['resSave']['ResponseID'].'</ContractID>
				</Contract>
			</Contracts>
			</Root>';
		$resConfirm = $this->request('Confirm', $xml);

		$order_info = $order->info;
		$order_info['request_pay'] = $xml;
		$order_info['responce_pay'] = $resConfirm;
		$order->info = $order_info;

		try {
			$resConfirm = array_pop($resConfirm);
			$resConfirm = array_pop($resConfirm);

			if (!$this->is_api_error($resConfirm)){
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
     * @param null $additionalInfo
     *
     * @return array
     * @throws Exception
     */
    public function downloadOrder(Orders $order, $additionalInfo=null){
		$log = [];
		if ($order->status == Orders::STATUS_PAYED_API) {
			$log[time()] = 'Старт загрузки информации из апи';

			$payer_model = $this->getHolder($order->calc_form->payer);

			$requestID = $payer_model->id . '-' . $order->program->id . '-' . time();

			$xml = '<?xml version="1.0" encoding="utf-8" ?>
<Root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="RequestGetPDF.xsd">
<ContractsCount>1</ContractsCount>
<Contracts>
   <Contract>
      <RequestID>' . $requestID . '</RequestID>
      <ContractID>' . $order->info['resSave']['ResponseID'] . '</ContractID>
    </Contract>
</Contracts>
</Root>';
			$resGetPDF = $this->request('GetPDF', $xml);
			$resGetPDF = array_pop($resGetPDF);
			$resGetPDF = array_pop($resGetPDF);

			$log[time()] = 'Получен ответ: <pre>' . print_r($resGetPDF, true) . '</pre>';

			if ($this->is_api_error($resGetPDF)) {
				throw new Exception($resGetPDF['ErrorList']['ErrorInfo']['ErrorMessage'],
					$resGetPDF['ErrorList']['ErrorInfo']['ErrorCode']);
			}

			$folder = $this->getOrderFolder($order);
			$file = $folder . 'police.pdf';

			$log[time()] = 'Сохранение полиса из base64 в ' . $file;

			file_put_contents($file, base64_decode($resGetPDF['Data']));

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
		$price = Price::findOne(['id' => $program_id]);
		/** @var Price $price */

		$additionalRisksSearch = [];
		$addSport = 'false';
		foreach($form->params as $param){
			if ($param->handler->slug == FilterParamPrototype::SLUG_SPORT && $param->handler->checked) {
				$addSport = 'true';
			}

			$processed_search = [];
			if ($param->handler->checked) {
				$_links = Risk2internal::findAll(['internal_id' => $param->risk_id]);
				foreach ($_links as $link) {
					$apiRisk = $link->risk;
					/** @var $apiRisk Risk */

					if (!in_array($apiRisk->id, $processed_search)) {
						array_push($processed_search, $apiRisk->id);
						/** @var $condition \common\modules\ApiVtb\models\AdditionalCondition */
						$modelClass = $apiRisk->class;
						if (class_exists($modelClass) && is_subclass_of($modelClass, prototype::className())) {
							$model = new $modelClass([
								'form' => $form,
								'param' => $param,
								'price' => $price
							]);

							$model->applyApiSearch($additionalRisksSearch);
						}
					}
				}
			}
		}
		ksort($additionalRisksSearch);

		$payer_model = $this->getHolder($form->payer);

		$requestID = $payer_model->id.'-'.$program_id.'-'.time();

		$from = \DateTime::createFromFormat('d.m.Y', trim($form->dateFrom));
		$to   = \DateTime::createFromFormat('d.m.Y', trim($form->dateTo));

		$startDay = $from->format('Y-m-d');
		$lastDay  = $to->format('Y-m-d');

		$countries = '';
		foreach ($form->countries as $countryID){
			$c2d = Country2dict::findOne(['internal_id' => $countryID]);
			if ($c2d){
				$countries.="<Country>".$c2d->apiModel->code."</Country>\n\r";
			}
		}

		$persons = '';
		foreach ($form->travellers as $traveller){
			$persons .= "<InsuredPerson><PersonID>".$this->getHolder($traveller)->id."</PersonID><Dob>".$traveller->birthday."</Dob></InsuredPerson>";
		}

		$xmlCalc = '<?xml version="1.0" encoding="UTF-8" ?>
<Root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="RequestCalc.xsd">
	<Contracts>
		<Contract>
			<RequestID>'.$requestID.'</RequestID>
			<ProductID>voyage</ProductID>
			<PartnerCode>'.$this->partnerCode.'</PartnerCode>
			<Duration>'.$form->dayCount.'</Duration>
			<StartDate>'.$startDay.'</StartDate>
			<EndDate>'.$lastDay.'</EndDate>
			<Sport>'.$addSport.'</Sport>
			<Countries>
				'.$countries.'
			</Countries>
			<Currency>EUR</Currency>
			<MultiTrip>false</MultiTrip>
			<InsuredPersons>
				'.$persons.'
			</InsuredPersons>
			<Period></Period>
			<Coverage>
				<Me>
					<ProgramCode>'.$price->program->code.'</ProgramCode>
					<InsSum>'.$price->amount->amount.'</InsSum>
				</Me>
				'.implode('',$additionalRisksSearch).'
			</Coverage>
			<ErrorList>
				<ErrorInfo>
					<ErrorCode>1</ErrorCode>
					<ErrorMessage>No Errors</ErrorMessage>
				</ErrorInfo>
			</ErrorList>
		</Contract>
	</Contracts>
</Root>';
		$resCalc = $this->request('Calc', $xmlCalc);
		$resCalc = array_pop($resCalc);
		$resCalc = array_pop($resCalc);
		if ($this->is_api_error($resCalc)){
			throw new Exception($resCalc['ErrorList']['ErrorInfo']['ErrorMessage'],$resCalc['ErrorList']['ErrorInfo']['ErrorCode']);
		}

		$additionalRisksSave = [];
		foreach($form->params as $param){
			$processed_save = [];
			if ($param->handler->checked) {
				$_links = Risk2internal::findAll(['internal_id' => $param->risk_id]);
				foreach ($_links as $link) {
					$apiRisk = $link->risk;
					/** @var $apiRisk Risk */

					if (!in_array($apiRisk->id, $processed_save)) {
						array_push($processed_search, $apiRisk->id);
						/** @var $condition \common\modules\ApiVtb\models\AdditionalCondition */
						$modelClass = $apiRisk->class;
						if (class_exists($modelClass) && is_subclass_of($modelClass, prototype::className())) {
							$model = new $modelClass([
								'form' => $form,
								'param' => $param,
								'price' => $price
							]);

							$model->applyApiSave($additionalRisksSave, $resCalc);
						}
					}
				}
			}
		}
		ksort($additionalRisksSave);

		$order = new Orders();
		$order->api_id      = $this->model->id;
		$order->price       = $resCalc['TotalPremium'];
		$order->currency_id = Currency::findOne(['char_code' => 'EUR'])->id;
		$order->status      = Orders::STATUS_NEW;
		$order->holder_id   = $payer_model->id;
		$order->info        = [
			'xmlCalc' => $xmlCalc,
			'resCalc' => $resCalc,
			'xmlSave' => 'null',
			'resSave' => 'null',
		];
		$order->calc_form   = $form;
		$order->program     = $price;
		if (!$order->save()) {
			throw new Exception(strip_tags(Html::errorSummary($order)), 500);
		}

		$requestID = $payer_model->id.'-'.$program_id.'-'.time();
		$insurers  = ''; $insurers_ids = [];
		foreach ($form->travellers as $key => $traveller){
			$person = (isset($resCalc['InsuredPersons'])
 		            && isset($resCalc['InsuredPersons']['InsuredPerson'])
 		            && isset($resCalc['InsuredPersons']['InsuredPerson'][$key])
					)?$resCalc['InsuredPersons']['InsuredPerson'][$key]:null;
			if (!$person) {
				$person = (isset($resCalc['InsuredPersons'])
	                    && isset($resCalc['InsuredPersons']['InsuredPerson'])
	                    && isset($resCalc['InsuredPersons']['InsuredPerson']['PersonID'])
						)?$resCalc['InsuredPersons']['InsuredPerson']:null;
			}
			if (!$person){
				throw new Exception("Bad API response", 500);
			}
			$insurers_model = $this->getHolder($traveller);
			$insurers_ids[] = $insurers_model->id;
			$insurers .= "<InsuredPerson>				
	<IsInsurer>". (($payer_model->id == $insurers_model->id)?"true":"false") ."</IsInsurer>
	<Person>
		<PersonType>0</PersonType>
		<FirstNameRus>".$traveller->first_name."</FirstNameRus>
		<LastNameRus>".$traveller->last_name."</LastNameRus>
		<FirstNameLat>".$traveller->first_name."</FirstNameLat>
		<LastNameLat>".$traveller->last_name."</LastNameLat>
		<Birthday>".$traveller->birthday."</Birthday>
		<Isresident>true</Isresident>
		<Phones></Phones>
		<Documents>
			<Document>
				<DocTypeId>20009</DocTypeId>			
				<DocSeries>-</DocSeries>			
				<DocNumber>-</DocNumber>			
			</Document>		
		</Documents>
	</Person>
	<Coverage>
		<Me>
			<InsSum>".$price->amount->amount."</InsSum>
			<InsPrem>".$person['Coverage']['Me']['InsPrem']."</InsPrem>
		</Me>";
			if (isset($person['Coverage']['Accident'])) {
				$insurers .= "<Accident>";
				$insurers .= "<InsSum>".$person['Coverage']['Accident']['InsSum']."</InsSum>";
				$insurers .= "<InsPrem>".$person['Coverage']['Accident']['InsPrem']."</InsPrem>";
				$insurers .= "</Accident>";
			}
			if (isset($person['Coverage']['Amulex'])) {
				$insurers .= "<Amulex>";
				$insurers .= "<InsSum>".$person['Coverage']['Amulex']['InsSum']."</InsSum>";
				$insurers .= "<InsPrem>".$person['Coverage']['Amulex']['InsPrem']."</InsPrem>";
				$insurers .= "</Amulex>";
			}
			if (isset($person['Coverage']['Cl'])) {
				$insurers .= "<Cl>";
				$insurers .= "<InsPrem>".$person['Coverage']['Cl']['InsPrem']."</InsPrem>";
				$insurers .= "</Cl>";
			}
			if (isset($person['Coverage']['RaceDelay'])) {
				$insurers .= "<RaceDelay>";
				$insurers .= "<InsSum>".$person['Coverage']['RaceDelay']['InsSum']."</InsSum>";
				$insurers .= "<InsPrem>".$person['Coverage']['RaceDelay']['InsPrem']."</InsPrem>";
				$insurers .= "</RaceDelay>";
			}
	$insurers .= "
	</Coverage>
</InsuredPerson>
";
		}

		$first_braket = mb_strpos($payer_model->phone, '(');
		$second_braket = mb_strpos($payer_model->phone, ')');

		if (!$first_braket) $first_braket = 2;
		if (!$second_braket) $second_braket = 5;

		$phone_code   = mb_substr($payer_model->phone, 0, $first_braket);
		$phone_prefix = mb_substr($payer_model->phone, $first_braket+1, $second_braket-$first_braket-1);
		$phone        = mb_substr($payer_model->phone, $second_braket+1);

		$xmlSave = '<?xml version="1.0" encoding="utf-8"?>
<Root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="RequestSave.xsd">
	<Info source="'.$this->login.'" export_date="'.date('d.m.Y').'" export_id="'.$order->id.'" export_version="1"/>
	<Contractcount>1</Contractcount>
	<Contracts>
		<Contract>
			<!-- договор в котором страхователь=застрахованному-->
			<RequestID>'.$requestID.'</RequestID>
			<ProductID>voyage</ProductID>
			<PartnerCode>'.$this->partnerCode.'</PartnerCode>
			<Series/>
			<Number/>
			<IssueDate>'.date('Y-m-d').'</IssueDate>
			<StartDate>'.$startDay.'</StartDate>
			<EndDate>'.$lastDay.'</EndDate>
			<MultiTrip>false</MultiTrip>
			<Duration>'.$form->dayCount.'</Duration>
			<Currency>
				<CurCode>'.$resCalc['Currency']['CurCode'].'</CurCode>
				<CurRate>'.$resCalc['Currency']['CurRate'].'</CurRate>
				<CurDate>'.date('Y-m-d').'</CurDate>
			</Currency>
			<Sport>'.$addSport.'</Sport>
			<Countries>
				'.$countries.'
			</Countries>
			<Coverage>
				<Me>
					<ProgramCode>'.$price->program->code.'</ProgramCode>
					<InsSum>'.$price->amount->amount.'</InsSum>
					<InsPrem>'.$resCalc['Coverage']['Me']['InsPrem'].'</InsPrem>
				</Me>
				'.implode('',$additionalRisksSave).'
			</Coverage>
			<TotalPremium>'.$resCalc['TotalPremium'].'</TotalPremium>
			<Insurer>
				<Person>
					<PersonType>0</PersonType>
					<FirstNameRus>'. $payer_model->first_name .'</FirstNameRus>
					<LastNameRus>'.$payer_model->last_name.'</LastNameRus>
					<FirstNameLat>'. $payer_model->first_name .'</FirstNameLat>
					<LastNameLat>'. $payer_model->last_name .'</LastNameLat>
					<Birthday>'. $payer_model->birthday .'</Birthday>
					<Phones>
						<Phone TypeId="2" CountryCode="'.$phone_code.'" Prefix="'.$phone_prefix.'">'.$phone.'</Phone>
					</Phones>
					<Documents>						
							<Document>
								<DocTypeId>20003</DocTypeId>
								<DocTypeName>Загранспаспорт РФ</DocTypeName>
								<DocSeries>'.$payer_model->passport_seria.'</DocSeries>
								<DocNumber>'.$payer_model->passport_no.'</DocNumber>
								<DocIssuedBy/>
								<DocIssueDate/>
							</Document>
					</Documents>
					<Emails>
						<Email Email_Type="1">'.$payer_model->email.'</Email>
					</Emails>
				</Person>
			</Insurer>
			<!-- Количество застрахованных по договору-->
			<InsuredPersonsCount>'.$form->travellersCount.'</InsuredPersonsCount>
			<InsuredPersons>
				'.$insurers.'
			</InsuredPersons>
		</Contract>
	</Contracts>
</Root>
';

		$resSave = $this->request('Save', $xmlSave);
		$resSave = array_pop($resSave);
		$resSave = array_pop($resSave);

		if ($this->is_api_error($resSave)){
			throw new Exception($resSave['ErrorList']['ErrorInfo']['ErrorMessage'],$resSave['ErrorList']['ErrorInfo']['ErrorCode']);
		}

		$order->info        = [
			'xmlCalc' => $xmlCalc,
			'resCalc' => $resCalc,
			'xmlSave' => $xmlSave,
			'resSave' => $resSave,
		];
		if (!$order->save()) {
			throw new Exception(strip_tags(Html::errorSummary($order)), 500);
		}

		return $order;
	}

    /**
     * Сервисный метод обнаружения ошибки в ответе АПИ
     *
     * @param $responce
     *
     * @return bool
     */
    public function is_api_error($responce){
		if ($responce['ErrorList'] && $responce['ErrorList']['ErrorInfo'] && $responce['ErrorList']['ErrorInfo']['ErrorMessage']){
			if ($responce['ErrorList']['ErrorInfo']['ErrorMessage'] != 'No error'){
				return true;
			}
			return false;
		} else return false;
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
			$holder = new Person(['scenario' => Person::SCENARIO_PAYER_VTB]);
			$holder->load(ArrayHelper::toArray($info), '');
			$holder->save();
		}
		return $holder;
	}
}
