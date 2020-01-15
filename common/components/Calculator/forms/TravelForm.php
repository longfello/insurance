<?php

// namespace frontend\models;
namespace common\components\Calculator\forms;

use api\components\Rest\Post\CalcTravel;
use api\components\Rest\RestMethod;
use common\models\GeoCountry;
use common\models\Risk;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterSolution;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\prototype;
use frontend\models\PersonInfo;
use common\models\Api;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

/**
 * Class TravelForm Форма туристического страхования
 * @package common\components\Calculator\forms
 */
class TravelForm extends prototype
{
	/** @var string Слюг (псевдоним) типа страхования */
	public $slug = self::SLUG_TRAVEL;

	/** @var integer[] Массив id выбранных стран (вместе с территориями) */
    public $countries = [];
    /** @var GeoCountry[] Массив моделей выбранных стран  (вместе с территориями) */
    public $countriesModels = [];
	/** @var integer[] Массив id выбранных стран вместе с развернутыми странами из территорий */
    public $countriesOverall = [];
	/** @var integer[] Массив id выбранных территорий */
    public $territories = [];
	/** @var integer[] Массив моделей выбранных территорий */
    public $territoriesModels = [];
	/** @var integer[] Массив id выбранных стран, развернутых из территорий */
    public $countriesFromTerritories = [];
	/** @var FilterSolution[] Массив доступных готовых решений*/
	public $solutions = [];
	/** @var integer[] Доступные api для выбранного решения */
	public $apiIds = [];

    /**
     * @var string Даты поездки указанные через разделитель "-"
     */
    public $dates;
    /**
     * @var string Дата начала в формате d.m.Y
     */
    public $dateFrom;
    /**
     * @var string Дата окончания в формате d.m.Y
     */
    public $dateTo;
    /**
     * @var int количество дней
     */
    public $dayCount = 0;
    /**
     * @var int количество путешественников
     */
    public $travellersCount = 1;
    /**
     * @var int Выбранное решение, 0 если не выбрано
     */
    public $solution = 0;
	/**
	 * @var PersonInfo[] Информация о путешественниках
	 */
    public $travellers = [];
    /**
     * @var FilterParam[] Параметры фильтра
     */
    public $params = [];

    /**
     * Принудительный расчет по удалённым апи
     * @var bool
     */
    public $forceRemoteCalc = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
        	[['countries', 'dateFrom', 'dateTo'], 'required'],
        	[['dateFrom', 'dateTo'], 'date', 'format' => 'd.m.Y'],
	        ['dateFrom', 'validateDate'],
	        ['dates', 'validateDates'],
	        ['travellersCount', 'integer'],
	        ['travellersCount', 'default', 'value' => 1],

	        // scenario "calc"
	        [['travellersCount', 'dates'], 'required', 'on' => self::SCENARIO_CALC],
	        [['travellers'], 'required', 'on' => self::SCENARIO_PREPAY],
	        [['travellers'], 'validateTravellers', 'on' => self::SCENARIO_PREPAY],
        	[['countries', 'dates', 'dateFrom', 'dateTo', 'travellersCount'], 'safe']
        ]);
    }

    /**
     * Валидация дат поездки
     * @param $attribute
     * @param $params
     */
    public function validateDate($attribute, $params){
	    $from = \DateTime::createFromFormat('d.m.Y', trim($this->dateFrom));
	    $to   = \DateTime::createFromFormat('d.m.Y', trim($this->dateTo));

	    if ($to && $from){
		    if ($from->getTimestamp() > $to->getTimestamp()){
			    $this->addError('dateFrom', 'Значение «Начало поездки» должно быть меньше или равно значения «Конец поездки»');
		    }

		    if ($from < new \DateTime()){
			    $this->addError('dateFrom', 'Значение «Начало поездки» не может быть в прошлом');
		    }
	    }

    }

    /**
     * Валидация информации о путешественниках
     * @param $attribute
     * @param $params
     */
    public function validateTravellers($attribute, $params){
    	if (is_array($this->travellers) && count($this->travellers) > 0){
		    foreach ($this->travellers as $key => $traveller){
		    	/* @var $traveller \frontend\models\PersonInfo **/
		    	$traveller->scenario = PersonInfo::SCENARIO_TRAVELLER;
		    	if (!$traveller->validate()){
				    $errors = [];
		    		foreach ($traveller->getErrors() as $field => $message){
					    $errors[$field.'-'. ($key + 1)] = $message;
				    }
				    $this->addErrors($errors);
			    }
		    }
	    } else { $this->addError($attribute, 'Не заданы путешественники'); }
    }

	/**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'countries'       => 'Страна путешествия',
            'dates'           => 'Даты поездки', // Дублирует две последующие
            'dateFrom'        => 'Начало поездки',
            'dateTo'          => 'Конец поездки',
            'travellersCount' => 'Количество путешественников:',
        ];
    }

    /**
     * Валидация строки интервала дат
     * @param $attribute
     * @param $params
     */
    public function validateDates($attribute, $params){
    	if (strpos($this->dates, '-')){
		    list($from, $to) = explode('-', $this->dates);
		    $from = \DateTime::createFromFormat('d.m.Y', trim($from));
		    $to   = \DateTime::createFromFormat('d.m.Y', trim($to));
		    if ($to && $from){
		    	return;
		    } else {
		    	if (!$from) $this->addError($attribute, 'Неверно введена начальная дата');
		    	if (!$to)   $this->addError($attribute, 'Неверно введена конечная дата');
		    }
	    } else {
		    $this->addError($attribute, 'Неверно введен интервал дат');
	    }
    }

    /**
     * Получение дат путешествия в формате JSON
     * @return string
     */
    public function datesAsJson(){
    	return Json::encode([
		    Yii::$app->formatter->asDate(\DateTime::createFromFormat('d.m.Y', trim($this->dateFrom)), 'php:Y-m-d'),
     	    Yii::$app->formatter->asDate(\DateTime::createFromFormat('d.m.Y', trim($this->dateTo)), 'php:Y-m-d'),
	    ]);
    }

    /** @inheritdoc */
    public function load( $data, $formName = null ) {
	    $res =  parent::load( $data, $formName );
	    $this->loadDates();
	    $this->loadRisks();
	    $this->loadTravellers(Yii::$app->request->post('traveller'));
	    $this->loadCountries();
	    $this->loadSolutions();

	    return $res;
    }

    /**
     * @inheritdoc
     */
    public function loadFromUrl($url = null){

    }

    /**
     * @inheritdoc
     */
    public function createUrl($scenario = self::SCENARIO_CALC){

    }

    /**
     * Загрузка данных с Rest API
     * @param RestMethod $apiMethod объект метода
     */
    public function loadFromApi(RestMethod $apiMethod){
        /** @var $apiMethod CalcTravel */
        /** @todo Расширить загрузку параметрами предварительного заказа */

        // Загрузка дат
        $this->dateFrom = $apiMethod->date_from->format('d.m.Y');
        $this->dateTo = $apiMethod->date_to->format('d.m.Y');
        $this->loadDates();

        // Загрузка рисков
        foreach ($apiMethod->filters as $key => $filter){
            $this->params[$filter->id] = $filter;
        }

        // Загрузка путешественников
        $this->travellers = [];
        $this->travellersCount = count($apiMethod->travellers);
        foreach ($apiMethod->travellers as $info){
            $traveller  = new PersonInfo(['scenario' => PersonInfo::SCENARIO_TRAVELLER]);
            $traveller->first_name = isset($info['first_name'])?$info['first_name']:'';
            $traveller->last_name = isset($info['last_name'])?$info['last_name']:'';
            $age = isset($info['age'])?$info['age']:false;
            if ($age){
                $traveller->birthday = date("Y-m-d", strtotime("-{$age} year", time()));
            }
            $this->travellers[] = $traveller;
        }

        // Загрузка стран
        $this->countries = [];
        foreach ($apiMethod->country as $country){
            $this->countries[] = $country->id;
        }
        $this->loadCountries();

        // Загрузка готовых решений
		$this->loadSolutionFromApi($apiMethod->solution);

		// Загрузка API
        $ids = [];
        foreach($apiMethod->insurant as $api){
            $ids[] = $api->id;
        }
        if ($this->apiIds){
            $this->apiIds = array_intersect($this->apiIds, $ids);
        } else {
            $this->apiIds = $ids;
        }

    }


    /**
     * Перечень выбранных стран в виде строки
     * @return string
     */
    public function countriesAsString(){
    	$names = [];
    	foreach ($this->countriesModels as $model){
    		$names[] = $model->name;
	    }
	    return implode(', ', $names);
    }


    /**
     * Загрузка путешественников
     */
    private function loadTravellers($travellers){
	    if ($travellers){
	    	$this->travellersCount = 0;
            $this->travellers = [];
	    	foreach ($travellers['first_name'] as $key => $first_name){
			    $traveller = new PersonInfo(['scenario' => PersonInfo::SCENARIO_TRAVELLER]);
	    		$last_name = $travellers['last_name'][$key];
	    		$birthday = $travellers['birthday'][$key];
	    		$gender = $travellers['gender'][$key];

	    		$traveller->load(compact('first_name', 'last_name', 'birthday', 'gender'), '');
	    		$this->travellers[] = $traveller;
	    		$this->travellersCount++;
		    }
	    }
    }

    /**
     * Загрузка дат
     */
    private function loadDates(){
	    if (($this->dateFrom || $this->dateTo) && (!$this->dates)){
		    // Если заданы даты и не задан интервал
		    $this->dates = $this->dateFrom.' - '.$this->dateTo;
	    } else {
		    // Если задан интервал
		    if($this->dates && strpos($this->dates, '-')){
			    list($from, $to) = explode('-', $this->dates);
			    $from = \DateTime::createFromFormat('d.m.Y', trim($from));
			    $to   = \DateTime::createFromFormat('d.m.Y', trim($to));
			    if ($to && $from){
				    $this->dateFrom = Yii::$app->formatter->asDate($from, 'php:d.m.Y');
				    $this->dateTo   = Yii::$app->formatter->asDate($to, 'php:d.m.Y');
			    } else {
				    $this->dates = null;
			    }
		    }
	    }

	    if ($this->dateFrom && $this->dateTo){
		    $ts1 = \DateTime::createFromFormat('d.m.Y', trim($this->dateFrom));
     	    $ts2 = \DateTime::createFromFormat('d.m.Y', trim($this->dateTo));

     	    if ($ts1 && $ts2) {
	            $this->dayCount = 1 + round(abs($ts1->getTimestamp() - $ts2->getTimestamp())/60/60/24);
            }
	    }
    }

    /**
     * Загрузка рисков
     */
    private function loadRisks(){
    //	$this->params = [];
	    $filters = \common\components\Calculator\models\travel\FilterParam::find()->all();
	    foreach($filters as $filter){
		    /** @var $filter \common\components\Calculator\models\travel\FilterParam */
		    if (!isset($this->params[$filter->id]) && $filter && $filter->handler) {
			    $filter->handler->load();
			    $this->params[$filter->id] = $filter;
		    }
	    }
    }

    /**
     * Загрузка стран
     */
    private function loadCountries(){
	    $this->territories = $this->territoriesModels = $this->countriesOverall = $this->countriesFromTerritories = [];
    	$this->countriesModels = GeoCountry::findAll(['id' => $this->countries]);

	    foreach($this->countriesModels as $countryModel){
	    	if ($countryModel->type == GeoCountry::TYPE_TERRITORY){
	    		$this->territories[] = $countryModel->id;
	    		$this->territoriesModels[] = $countryModel;
	    		foreach ($countryModel->subCountries as $subcountry){
	    			$this->countriesOverall[] = $subcountry->id;
	    			$this->countriesFromTerritories[] = $subcountry->id;
			    }
		    }
	    	if ($countryModel->type == GeoCountry::TYPE_COUNTRY){
			    $this->countriesOverall[] = $countryModel->id;
		    }
	    }
	    array_unique($this->countriesOverall);
	    array_unique($this->countriesFromTerritories);
    }

    /**
     * Загрузка готовых решений
     */
    private function loadSolutions() {
		$solutions = FilterSolution::find()
			->alias('fs')
			->select("fs.*, count(DISTINCT fs2c.country_id) as cnt")
			->innerJoin('filter_solution2country fs2c', 'fs2c.filter_solution_id = fs.id')
			->where(['fs2c.country_id' => $this->countriesOverall])
			->andWhere(['fs.is_front' => 1])
			->having(['cnt'=>count($this->countriesOverall)])
			->groupBy('fs.id')
			->orderBy('name')
			->all();

		if ($solutions) $this->solutions = $solutions;

		$this->solution = \Yii::$app->request->post('filter_solution', null);
		if ($this->solution) {
			$this->apiIds = [];
			$apis = Api::find()
				->alias('api')
				->select("api.id")
				->innerJoin('filter_solution2api fs2a', 'fs2a.api_id = api.id')
				->where(['fs2a.filter_solution_id' => $this->solution])
				->all();

			foreach ($apis as $one_api) {
				$this->apiIds[] = $one_api->id;
			}
		}

	}


	/**
	 * Загрузка готовых решений из api
	 * @param $solution FilterSolution готовое решение
	 */
	private function loadSolutionFromApi($solution) {
	    if ($solution){
            $this->apiIds = [];
            $apis = Api::find()
                       ->alias('api')
                       ->select("api.id")
                       ->innerJoin('filter_solution2api fs2a', 'fs2a.api_id = api.id')
                       ->where(['fs2a.filter_solution_id' => $solution->id])
                       ->all();

            foreach ($apis as $one_api) {
                $this->apiIds[] = $one_api->id;
            }

            $solution_params = [];
            foreach ($solution->filterSolution2params as $one) {
                $solution_params[$one->param_id] = $one->value;
            }

            $filters = \common\components\Calculator\models\travel\FilterParam::find()->orderBy(['sort_order' => SORT_ASC])->all();
            foreach($filters as $filter) {
                /** @var $filter \common\components\Calculator\models\travel\FilterParam */
                if ($filter && $handler = $filter->getHandler()) {
                    $handler->load($solution_params);
                    $this->params[$filter->id] = $filter;
                }
            }
        }
	}

    /**
     * Изменение варианта параметра
     * @param $class string Класс параметра фильтра
     * @param $value mixed Устанавливаемое значение. Его тип зависит от того, какой тип параметра принимает класс параметра фильтра для варианта
     *
     * @return bool был ли установлен вариант
     */
    public function changeParamVariant($class, $value){
	    $filter = FilterParam::findOne(['class' => $class]);
    	if ($filter && $filter->handler) {
    		if (!isset($this->params[$filter->id])){
			    $filter->handler->load();
			    $this->params[$filter->id] = $filter;
		    }
		    $this->params[$filter->id]->handler->setVariant($value);
    		return true;
	    }
	    return false;
    }
}
