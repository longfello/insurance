<?php
/**
 * Copyright (c) kvk-group 2018.
 */

namespace common\modules\ApiSberbank\components;


use common\components\ApiModule;
use common\models\ProgramResult;
use common\modules\ApiSberbank\models\AdditionalRisk2internal;
use common\modules\ApiSberbank\models\Territory;
use common\modules\ApiSberbank\models\Program;
use common\modules\ApiSberbank\models\Territory2Dict;
use common\modules\ApiSberbank\Module;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\TravelForm;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class ProgramSearch поиск программ страхования
 * @package common\modules\ApiSberbank\components
 */
class ProgramSearch extends Component {
	/**
	 * @var TravelForm модель формы
	 */
	public $form;
	/**
	 * @var Module модуль АПИ
	 */
	public $module;
	/**
	 * @var ActiveQuery запрос поиска
	 */
	public $query;

	/**
	 * Поиск программы страхования
	 * @param bool $with_price
	 *
	 * @return ProgramResult|null
	 */
	public function findAll($with_price = true){

		if ($this->form->travellersCount>6) return null;

		$this->query = Program::find()->select('program.*')->from(['program' => 'api_sberbank_program']);
		$this->query->InnerJoin("cost_interval ci", 'ci.id = program.cost_interval_id');
		$this->query->orderBy(['ci.from' => ($with_price)?SORT_ASC:SORT_DESC]);

		$this->processCountries();

		foreach($this->form->params as $param){
			if ($param->handler->checked && ($with_price || $param->handler->slug!=FilterParamPrototype::SLUG_SUM)) {
				$this->processParam($param);
			}
		}

		//echo $this->query->createCommand()->rawSql;
		//echo "<br/>";

		$result = $this->query->one();
		/** @var $result Program|null */
		if ($result) {
			if (in_array($this->form->scenario, [TravelForm::SCENARIO_PREPAY, TravelForm::SCENARIO_PAYER, TravelForm::SCENARIO_PAY])) {
				return $this->adapt($result, ApiModule::CALC_API);
			} else {
				return $this->adapt($result);
			}
		} elseif ($with_price) {
			return $this->findAll(false);
		} else return null;
	}

	/**
	 * Обработка территорий
	 *
	 * @param Program $program
	 * @return Territory|\yii\db\ActiveRecord
	 */
	public function findTerritory(Program $program) {
		$areas_query = Territory::find()->select('insTerritory')->from(['t' => 'api_sberbank_territory']);
		$areas_query->InnerJoin("api_sberbank_territory2program t2p", 't2p.territory_id = t.id');
		$areas_query->InnerJoin("api_sberbank_territory2dict t2d", 't2d.territory_id = t.id');
		$areas_query->where(['t.enabled'=>1])->andWhere(['t2p.program_id'=>$program->id])->andWhere(['t2d.internal_id'=>$this->form->countries]);

		return $areas_query->one();
	}

	/**
	 * Обработка стран
	 */
	public function processCountries(){
		if ($this->form->countries) {
			$subQuery = Program::find()
				->alias('p')
				->select("p.id, count(DISTINCT t2d.internal_id) as cnt")
				->innerJoin('api_sberbank_territory2program t2p', 't2p.program_id = p.id')
				->leftJoin('api_sberbank_territory2dict t2d', ['and', ['t2d.territory_id' => new \yii\db\Expression('t2p.territory_id')], ['t2d.internal_id' => $this->form->countries]])
			    ->groupBy('p.id');
			$this->query->innerJoin(['jCountry' => $subQuery], 'program.id = jCountry.id AND jCountry.cnt = :cnt', [':cnt' => count($this->form->countries)]);
		}
	}

	/**
	 * Обработка параметров фильтра
	 * @param FilterParam $param
	 */
	public function processParam(FilterParam $param){
		switch($param->handler->slug){
			case FilterParamPrototype::SLUG_SUM:
				$this->processParamSum($param);
				break;
			case FilterParamPrototype::SLUG_PREGNANCY:
				$this->processParamPregnancy($param);
				break;
			case FilterParamPrototype::SLUG_CANCEL:
				$this->processParamCancel($param);
				$this->processParamNormal($param);
				break;
			default:
				$this->processParamNormal($param);
				break;
		}
	}

	/**
	 * Обработка параметра стоимости
	 * @param FilterParam $param
	 */
	public function processParamSum(FilterParam $param){
		$this->query->andWhere(['=', 'program.cost_interval_id', $param->handler->variant->id]);
	}

	/**
	 * Обработка ординарных параметров соответствия риску
	 * @param FilterParam $param
	 */
	public function processParamNormal(FilterParam $param){

		$slug = 'p2r'.$param->id;

		$subQuery = Program::find()
			->alias('p')
			->select("p.id", 'STRAIGHT_JOIN')
			->leftJoin("api_sberbank_program2risk p2r", 'p2r.program_id = p.id')
			->where(['p2r.risk_id' => $param->risk_id])
			->groupBy('p.id');

        $this->query->innerJoin([$slug => $subQuery], 'program.id = '.$slug.'.id');

        $riskLinks = AdditionalRisk2internal::find()->where(['internal_id' => $param->risk_id])->all();
        foreach ($riskLinks as $one){
            $riskTerritories = ArrayHelper::map($one->risk->territories, 'id', 'id');
            $riskCountries = ArrayHelper::map(Territory2Dict::findAll(['territory_id' => $riskTerritories]), 'internal_id', 'internal_id');
            foreach ($this->form->countries as $country){
                if (!in_array($country, $riskCountries)){
                    $this->query->andWhere("0=1");
                }
            }
        }
	}

	/**
	 * Обработка параметра беременности
	 * @param FilterParam $param
	 */
	public function processParamPregnancy(FilterParam $param){
		$this->query->andWhere('0=1');
	}

	/**
	 * Обработка параметра отмены поездки
	 * @param FilterParam $param
	 */
	public function processParamCancel(FilterParam $param) {
		if ($param->handler->variant['sick-list']) {
			$this->query->andWhere('0=1');
		}
	}

	/**
	 * Адаптация результата поиска в стандартное представление
	 * @param Program $program
	 * @param string $calcType
	 *
	 * @return ProgramResult
	 */
	public function adapt(Program $program, $calcType = ApiModule::CALC_LOCAL){
		//$cost = $this->module->calcPrice($price, $this->form, $calcType);

		//if ($price>0) {
		$model = new ProgramResult();

		$model->api_id        = $this->module->model->id;
		$model->program_id    = $program->id;
		$model->rate_expert   = $this->module->model->rate_expert;
		$model->rate_asn      = $this->module->model->rate_asn;
		$model->thumbnail_url = $this->module->model->thumbnail_base_url.'/'.$this->module->model->thumbnail_path;
		$model->rule_url      = $program->rule_base_url.'/'.$program->rule_path;
		$model->police_url    = $program->police_base_url.'/'.$program->police_path;
		$model->risks         = $program->getRisksAsArray($this->form);
		$model->actions       = $this->module->model->actions;
		$model->cost          = ($this->form->forceRemoteCalc) ? $this->module->calcPrice($program, $this->form, $calcType) :0;
		$model->phones        = $this->module->model->getPhonesAsArray();
		$model->calc          = $this->form;

		return $model;
		//} else return null;
	}

}