<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiInGosStrah;

use common\components\ApiModule;
use common\models\Currency;
use common\models\Orders;
use common\components\Calculator\forms\TravelForm;
use Yii;
use yii\base\Exception;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * Class Module Модуль страхования ИнГосСтрах
 * @package common\modules\ApiInGosStrah
 */
class Module extends ApiModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'common\modules\ApiInGosStrah\controllers';

    /**
     * @inheritdoc
     */
    public $uri = 'https://aisws.ingos.ru/sales-test/SalesService.svc?wsdl';
    /**
     * @var array данные для авторизации
     */
    public $auth = [
		'User' => "БУЛЛО СТРАХОВАНИЕ WS",
		'Password' => "qk7p8cvj"
	];

    /**
     * @return \SoapClient геттер клиента транспорта
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
			'label'=>Yii::t('backend', 'ИнГосСтрах'),
			'url' => '#',
			'icon'=>'<i class="fa fa-address-book"></i>',
			'options'=>['class'=>'treeview'],
			'items'=>[

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
     * Расчет стоимости АПИ
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
     * Обработка дополнительных условий
     * @param Price $price
     * @param TravelForm $form
     * @param $baseAmount
     *
     * @return int
     */
    protected function applyAdditionalConditions(Price $price, TravelForm $form, $baseAmount){
		/*
		$koef = 1;

		$conditions = \common\modules\ApiInGosStrah\models\AdditionalCondition::find()->all();
		foreach($conditions as $condition){
		*/
			/** @var $condition \common\modules\ApiInGosStrah\models\AdditionalCondition */
		/*
			$modelClass = $condition->class;
			if (class_exists($modelClass)){
				$model = new $modelClass([
					'form'   => $form,
					'params' => $condition->params,
					'baseAmount' => $baseAmount
				]);
		*/
				/** @var $model \common\modules\ApiInGosStrah\components\AdditionalConditionPrototype */
		/*
				$koef *= $model->getKoef();
			}
		}

		return $form->travellersCount * $baseAmount * $koef;
		*/
		return $form->travellersCount;
	}

    /**
     * @inheritdoc
     * @param $program_id
     *
     * @return mixed
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
     * @param null $additionalInfo
     *
     * @return array
     */
    public function downloadOrder(Orders $order, $additionalInfo=null){
		$log = [];
		$log[time()] = 'Старт загрузки информации из апи';

		//$payer_model = $this->getHolder($order->calc_form->payer);

		$responce = $order->info['responce'];
		$data = (array) $responce;
		$data = array_pop( $data );

		$url = $data->common->policyLink;

		$log[time()] = 'Сохранение полиса из '.$url;
		$this->wgetPolice($order, $url);

		$log[time()+1] = 'Завершено';
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

}
