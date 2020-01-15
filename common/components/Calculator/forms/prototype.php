<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 08.08.17
 * Time: 13:35
 */

namespace common\components\Calculator\forms;


use frontend\models\PersonInfo;
use common\models\ProgramResult;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class prototype Прототип модели формы калькулятора страховой компании
 * @package common\components\Calculator\forms
 */
class prototype extends Model {
    /**
     * Сценарий заглавной страницы калькулятора
     */
    const SCENARIO_HOME    = 'home';
    /**
     * Сценарий страницы калькулятора с результатами
     */
    const SCENARIO_CALC    = 'calc';
    /**
     * Сценарий предварительного оформления
     */
	const SCENARIO_PREPARE = 'prepare';
    /**
     * Сценарий подготовки оплаты
     */
	const SCENARIO_PREPAY  = 'prepay';
    /**
     * Сценарий оформления плательщика
     */
	const SCENARIO_PAYER   = 'payer';
    /**
     * Сценарий оплаты
     */
	const SCENARIO_PAY     = 'pay';
	/**
	 * Сценарий страницы лендинга калькулятора
	 */
	const SCENARIO_LANDING    = 'landing';
	/**
	 * Сценарий страницы лендинга калькулятора
	 */
	const SCENARIO_HOME_NEW    = 'home_new';

    /**
     * Идентификатор туристического страхования
     */
	const SLUG_TRAVEL      = 'travel';
    /**
     * Идентификатор Страхование жизни ипотечного заемщика
     */
	const SLUG_BORROWER    = 'borrower';
    /**
     * Идентификатор Страхование от несчастных случаев
     */
	const SLUG_ACCIDENT    = 'accident';
    /**
     * Идентификатор Страхование собственности
     */
	const SLUG_PROPERTY    = 'property';
    /**
     * Идентификатор Ипотечное страхование
     */
	const SLUG_MORTGAGE    = 'mortgage';
    /**
     * Идентификатор Накопительное страхование жизни
     */
	const SLUG_ENDOWMENT   = 'endowment';
    /**
     * Идентификатор Накопительное страхование жизни
     */
	const SLUG_INVESTMENT  = 'investment';

	/** @var string Слюг (псевдоним) типа страхования */
	public $slug;

	/**
	 * @var PersonInfo Плательщик
	 */
	public $payer;

    /**
     * @var bool согласие с правилами ресурса
     */
    public $agree;

	/**
	 * @var ProgramResult Результат поиска
	 */
	public $programResult;

    /**
     * Инициализация полей класса
     */
    public function init(){
		parent::init();

		$this->payer = new PersonInfo(['scenario' => PersonInfo::SCENARIO_PAYER]);
		$this->programResult = new ProgramResult();
	}

	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return array_merge(parent::rules(), [
			[['agree'], 'required', 'on' => self::SCENARIO_PREPAY, 'message' => 'Вы должны согласиться с условиями передачи информации'],
			[['agree'], 'required', 'on' => self::SCENARIO_PAYER, 'message' => 'Вы должны согласиться с условиями передачи информации'],
//	        [['agree'], 'compare', 'compareValue' => 1],
			[['payer'], 'validatePayer', 'on' => self::SCENARIO_PREPAY],
			[['payer'], 'validatePayer', 'on' => self::SCENARIO_PAYER],
		]);
	}


    /**
     * Валидация плательщика
     * @param $attribute
     * @param $params
     */
    public function validatePayer($attribute, $params){

		if($this->programResult->api_id==3) {
			$this->payer->scenario = PersonInfo::SCENARIO_PAYER_VTB;
		} elseif($this->programResult->api_id==6) {
			$this->payer->scenario = PersonInfo::SCENARIO_PAYER_SBERBANK;
		} else $this->payer->scenario = PersonInfo::SCENARIO_PAYER;

		if (!$this->payer->validate()){
			$errors = [];
			foreach ($this->payer->getErrors() as $field => $message){
				$errors[$field] = $message;
			}
			$this->addErrors($errors);
		}
	}

    /**
     * Описание соответствия правил валидации сценариям
     * @return array
     */
    public function scenarios() {
		$scenarios          = parent::scenarios();
		$scenarios[self::SCENARIO_HOME]    = [ 'countries', 'dateFrom', 'dateTo' ];
		$scenarios[self::SCENARIO_CALC]    = [ 'countries', 'dateFrom', 'dateTo', 'dates', 'travellersCount'];
		$scenarios[self::SCENARIO_PREPARE] = [ 'countries', 'dateFrom', 'dateTo', 'dates', 'travellersCount', 'travellers', 'payer', 'agree'];
		$scenarios[self::SCENARIO_PREPAY]  = [ 'travellers', 'payer', 'agree'];
		$scenarios[self::SCENARIO_PAY]     = [ 'travellers', 'payer', 'agree'];
		$scenarios[self::SCENARIO_LANDING] = [ 'countries', 'dateFrom', 'dateTo' ];
		$scenarios[self::SCENARIO_HOME_NEW]    = [ 'countries', 'dateFrom', 'dateTo', 'travellersCount' ];
		return $scenarios;
	}

    /**
     * Загрузка парамтров формы
     * @inheritdoc
     */
    public function load( $data, $formName = null ) {
		$res =  parent::load( $data, $formName );
		$this->loadPayer();

		return $res;
	}

    /**
     * Получение формы согласно указанного типа страхования
     * @param string $slug идентификатор типа страхования
     *
     * @return bool|TravelForm модель формы
     * @throws Exception если отсутствует соответствующая модель формы
     */
    public static function getForm($slug){
		$form = false;
		switch ($slug){
			case prototype::SLUG_TRAVEL:
				$form = new TravelForm();
				$form->scenario = TravelForm::SCENARIO_PREPARE;
		}

		if ($form){
			return $form;
		} else {
			throw new Exception('Unknown calc form type');
		}
	}

    /**
     * Загрузка плательщика
     */
    private function loadPayer(){
		$this->payer->load(\Yii::$app->request->post('payer'), '');
	}

    /**
     * Загрузка параметров калькулятора из урла
     * @param string|null $url
     */
    public function loadFromUrl($url = null){
    }

    /**
     * Создание УРЛ из параметров калькулятора
     * @param string $scenario
     */
    public function createUrl($scenario = self::SCENARIO_CALC){

    }
}