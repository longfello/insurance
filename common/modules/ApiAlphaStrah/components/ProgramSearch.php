<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 27.02.17
 * Time: 14:48
 */

namespace common\modules\ApiAlphaStrah\components;


use common\components\ApiModule;
use common\models\ProgramResult;
use common\modules\ApiAlphaStrah\models\Amount;
use common\modules\ApiAlphaStrah\models\Price;
use common\modules\ApiAlphaStrah\models\InsuranceProgramm;
use common\modules\ApiAlphaStrah\Module;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\TravelForm;
use yii\base\Component;
use yii\db\ActiveQuery;

/**
 * Class ProgramSearch Подбор программы
 * @package common\modules\ApiAlphaStrah\components
 */
class ProgramSearch extends Component {
	/**
	 * @var TravelForm Модель формы параметров
	 */
	public $form;
	/**
	 * @var Module Модуль апи
	 */
	public $module;
	/**
	 * @var ActiveQuery Поисковый запрос
	 */
	public $query;

	/**
     * Поиск программ по заданным критериям
	 * @param bool $with_price use price filter - повторить поиск без учета критерия стоимости, если результатов не было найдено
	 *
	 * @return null|ProgramResult
	 */
	public function findAll($with_price = true){
		$this->query = Price::find()->select('p.*', 'STRAIGHT_JOIN')->from(['p' => 'api_alpha_price'])->distinct();
		$this->query->leftJoin("api_alpha_insurance_programm aep", 'aep.insuranceProgrammID = p.program_id');
		$this->query->leftJoin("api_alpha_amount a", 'a.id = p.amount_id');
		if (count($this->form->countries)>1) {
			$this->query->orderBy(['a.amount' => (($with_price) ? SORT_ASC : SORT_DESC), 'p.price' => SORT_DESC]);
		} else {
			$this->query->orderBy(['a.amount' => (($with_price) ? SORT_ASC : SORT_DESC), 'p.price' => SORT_ASC]);
		}

		$this->processCountries();

		foreach($this->form->params as $param){
			if ($param->handler->checked && ($with_price || $param->handler->slug!=FilterParamPrototype::SLUG_SUM)) {
				$this->processParam($param);
			}
		}

		$result = $this->query->one();
		/** @var $result Price|null */
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
     * Обработка прараметра Страна
     */
    public function processCountries(){
		if ($this->form->countries) {
			/*
			$subQuery = Price::find()
				->alias('p')
				->select("p.program_id, count(distinct c2d.internal_id) as cnt")
				->leftJoin("api_alpha_region2country r2c", 'r2c.region_id = p.region_id')
				->leftJoin('api_alpha_country2dict c2d', 'c2d.api_id = r2c.country_id')
				->leftJoin('api_alpha_country c', 'c.countryId = r2c.country_id')
				->where(['c2d.internal_id' => $this->form->countries])->andWhere(['c.enabled'=>1])
				->groupBy('p.program_id, r2c.region_id');

			$this->query->innerJoin(['jCountry' => $subQuery], 'aep.insuranceProgrammID = jCountry.program_id AND jCountry.cnt = :cnt', [':cnt' => count($this->form->countries)]);
			*/

			$subQuery = Amount::find()
				->alias('a')
				->select("a.id, `c`.`region_id`, count(distinct c2d.internal_id) as cnt")
				->leftJoin("api_alpha_price p", 'p.amount_id = a.id')
				->leftJoin('api_alpha_country c', 'c.region_id = p.region_id')
				->leftJoin('api_alpha_country2dict c2d', 'c2d.api_id = c.countryId')
				->where(['c2d.internal_id' => $this->form->countries])->andWhere(['c.enabled'=>1])
				->groupBy('p.program_id, a.id, c.region_id');

			$this->query->innerJoin(['jCountry' => $subQuery], 'a.id = jCountry.id AND p.region_id = jCountry.region_id AND jCountry.cnt = :cnt', [':cnt' => count($this->form->countries)]);
			//$this->query->innerJoin(['jCountry' => $subQuery], 'a.id = jCountry.id AND p.region_id = jCountry.region_id');
		}
	}

    /**
     * Обработка параметров фильтра
     * @param FilterParam $param
     */
    public function processParam(FilterParam $param){
		switch($param->handler->slug){
			case FilterParamPrototype::SLUG_NORMAL:
				$this->processParamNormal($param);
				break;
			case FilterParamPrototype::SLUG_SUM:
				$this->processParamSum($param);
				break;
			case FilterParamPrototype::SLUG_PREGNANCY:
				$this->processParamPregnancy($param);
				break;
			case FilterParamPrototype::SLUG_NOTFRANCHISE:
				$this->processParamNotFranchise($param);
				break;
			case FilterParamPrototype::SLUG_REPATRIATION:
				$this->processParamRepatriation($param);
				break;
			case FilterParamPrototype::SLUG_SEARCH:
				$this->processParamSearch($param);
				break;
			case FilterParamPrototype::SLUG_CANCEL:
				$this->processParamCancel($param);
				break;
			case FilterParamPrototype::SLUG_CIVIL:
				$this->processParamCivil($param);
				break;
			case FilterParamPrototype::SLUG_ACCIDENT:
				$this->processParamAccident($param);
				break;
			case FilterParamPrototype::SLUG_LUGGAGE:
				$this->processParamLuggage($param);
				break;
		}
	}

    /**
     * Обработка параметра гражданской ответственности
     * @param FilterParam $param
     */
    public function processParamCivil(FilterParam $param){
		$slug = 'p2r'.$param->id;

		$subQuery = Price::find()
		                 ->alias('p')
		                 ->select("p.program_id", 'STRAIGHT_JOIN')
		                 ->leftJoin("api_alpha_struh_sum a", "a.id = p.civil_sum_id")
			             ->where("p.civil_sum_id = ".Price::SUM_INCLUDED." OR a.id IS NOT NULL")
		                 ->groupBy("p.program_id");
		$this->query->innerJoin([$slug => $subQuery], "aep.insuranceProgrammID = {$slug}.program_id");
	}

    /**
     * Обработка параметра несчастного случая
     * @param FilterParam $param
     */
    public function processParamAccident(FilterParam $param){
		$slug = 'p2r'.$param->id;

		$subQuery = Price::find()
		                 ->alias('p')
		                 ->select("p.program_id", 'STRAIGHT_JOIN')
		                 ->leftJoin("api_alpha_struh_sum a", "a.id = p.accident_sum_id")
			             ->where("p.accident_sum_id = ".Price::SUM_INCLUDED." OR a.id IS NOT NULL")
		                 ->groupBy("p.program_id");
		$this->query->innerJoin([$slug => $subQuery], "aep.insuranceProgrammID = {$slug}.program_id");
	}

    /**
     * Обработка параметра багажа
     * @param FilterParam $param
     */
    public function processParamLuggage(FilterParam $param){
		$slug = 'p2r'.$param->id;

		$subQuery = Price::find()
		                 ->alias('p')
		                 ->select("p.program_id", 'STRAIGHT_JOIN')
		                 ->leftJoin("api_alpha_struh_sum a", "a.id = p.luggage_sum_id")
			             ->where("p.luggage_sum_id = ".Price::SUM_INCLUDED." OR a.id IS NOT NULL")
		                 ->groupBy("p.program_id");
		$this->query->innerJoin([$slug => $subQuery], "aep.insuranceProgrammID = {$slug}.program_id");
	}

    /**
     * Обработка ординарного параметра по соответствию риску
     * @param FilterParam $param
     */
    public function processParamNormal(FilterParam $param){
		$slug = 'p2r'.$param->id;

		$subQuery = Price::find()
			->alias('p')
			->select("p.program_id", 'STRAIGHT_JOIN')
			->leftJoin("api_alpha_risk2program r2p", 'r2p.program_id = p.program_id')
			//->leftJoin('api_alpha_risk r2i', 'r2i.riskID = r2p.risk_id')
			//->where("CONCAT(',',r2i.parent_id,',') LIKE '%,".$param->risk_id.",%'")
			->where("CONCAT(',',r2p.parent_id,',') LIKE '%,".$param->risk_id.",%'")
			->groupBy('p.program_id');

		$this->query->innerJoin([$slug => $subQuery], 'aep.insuranceProgrammID = '.$slug.'.program_id');
	}

    /**
     * Обработка параметра суммы
     * @param FilterParam $param
     */
    public function processParamSum(FilterParam $param){
		if ($param && $param->handler && $param->handler->variant) {
			$subQuery = Price::find()
				->alias('p')
				->select("p.id", 'STRAIGHT_JOIN')
				->leftJoin("api_alpha_amount a", 'a.id = p.amount_id')
				//->where(['between', 'a.amount', $param->handler->variant->from, $param->handler->variant->to])
				->where(['>=', 'a.amount', $param->handler->variant->from]);
			$this->query->innerJoin(['amount' => $subQuery], 'p.id = amount.id');
		}
	}

    /**
     * Обработка параметра беременности
     * @param FilterParam $param
     */
    public function processParamPregnancy(FilterParam $param){
		echo 'processParamPregnancy';
		$slug = 'p2r'.$param->id;

		$subQuery = Price::find()
			->alias('p')
			->select("p.program_id", 'STRAIGHT_JOIN')
			->leftJoin("api_alpha_risk2program r2p", 'r2p.program_id = p.program_id')
			//->leftJoin('api_alpha_risk r2i', 'r2i.riskID = r2p.risk_id')
			//->where("CONCAT(',',r2i.parent_id,',') LIKE '%,".$param->risk_id.",%'")
			->where("CONCAT(',',r2p.parent_id,',') LIKE '%,".$param->risk_id.",%'")
			->groupBy('p.program_id');

		$this->query->innerJoin([$slug => $subQuery], 'aep.insuranceProgrammID = '.$slug.'.program_id');
		$this->query->andWhere('aep.pregnant_week>='.$param->handler->variant);
	}

    /**
     * Обработка параметра франшизы
     * @param FilterParam $param
     */
    public function processParamNotFranchise(FilterParam $param) {
	}

    /**
     * Обработка параметра репатриации
     * @param FilterParam $param
     */
    public function processParamRepatriation(FilterParam $param) {
	}

    /**
     * Обработка параметра поисковых работ
     * @param FilterParam $param
     */
    public function processParamSearch(FilterParam $param) {
		$this->query->andWhere('0=1');
	}

    /**
     * Обработка параметра отмены поездки
     * @param FilterParam $param
     */
    public function processParamCancel(FilterParam $param) {
		$amount = $param->handler->variant['amount'];
		$sick_list = $param->handler->variant['sick-list'];
		if ($amount>4000 || $sick_list) {
			$this->query->andWhere('0=1');
		}
	}

    /**
     * Адаптация результата поиска в стандартное представление
     * @param Price $price
     * @param string $calcType
     *
     * @return ProgramResult
     */public function adapt(Price $price, $calcType = ApiModule::CALC_LOCAL){
		$model = new ProgramResult();

		$model->api_id        = $this->module->model->id;
		$model->program_id    = $price->id;
		$model->rate_expert   = $this->module->model->rate_expert;
		$model->rate_asn      = $this->module->model->rate_asn;
		$model->thumbnail_url = $this->module->model->thumbnail_base_url.'/'.$this->module->model->thumbnail_path;
		$model->rule_url      = $price->program->rule_base_url.'/'.$price->program->rule_path;
		$model->police_url    = $price->program->police_base_url.'/'.$price->program->police_path;
		$model->risks         = $price->getRisksAsArray($this->form);
		$model->actions       = $this->module->model->actions;
		$model->cost          = $this->module->calcPrice($price, $this->form, $calcType);
		$model->phones        = $this->module->model->getPhonesAsArray();
		$model->calc          = $this->form;

		return $model;
	}

}