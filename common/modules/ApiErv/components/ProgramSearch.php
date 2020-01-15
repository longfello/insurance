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

namespace common\modules\ApiErv\components;


use common\components\ApiModule;
use common\models\ProgramResult;
use common\modules\ApiErv\models\Program;
use common\modules\ApiErv\models\Region2Country;
use common\modules\ApiErv\Module;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\TravelForm;
use yii\base\Component;
use yii\db\ActiveQuery;

/**
 * Class ProgramSearch Поиска программ
 * @package common\modules\ApiErv\components
 */
class ProgramSearch extends Component {
	/**
	 * @var TravelForm Модель формы параметров поиска
	 */
	public $form;
	/**
	 * @var Module Модуль АПИ
	 */
	public $module;

	/**
	 * @var ActiveQuery запрос программ
	 */
	public $query;

	/**
     * Поиск программ по заданным критериям
     * @param bool $with_price use price filter - повторить поиск без учета критерия стоимости, если результатов не было найдено
	 *
	 * @return null|ProgramResult
	 */
	public function findAll($with_price = true){
		$this->query = Program::find()->select('aep.*')->from(['aep' => 'api_erv_program'])->distinct();
		$this->query->orderBy(['aep.summa' => ($with_price)?SORT_ASC:SORT_DESC, 'aep.price' => SORT_ASC]);

		$this->processCountries();

		foreach($this->form->params as $param){
			if ($param->handler->checked && ($with_price || $param->handler->slug!=FilterParamPrototype::SLUG_SUM)) {
				$this->processParam($param);
			}
		}
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
     * Обработка параметра страна
     */
    public function processCountries(){
		if ($this->form->countries) {
			$subQuery = Region2Country::find()
			                          ->select("region_id, count(country_id) AS cnt")
			                          ->where(["country_id" => $this->form->countries])
			                          ->groupBy("region_id");
			$this->query->innerJoin(['jCountry' => $subQuery], 'aep.region_id = jCountry.region_id AND jCountry.cnt = :cnt', [':cnt' => count($this->form->countries)]);
		}
	}

    /**
     * Обработка параметров
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
			case FilterParamPrototype::SLUG_NOTFRANCHISE:
				$this->processParamNotFranchise($param);
				break;
			case FilterParamPrototype::SLUG_REPATRIATION:
				$this->processParamRepatriation($param);
				break;
			case FilterParamPrototype::SLUG_SEARCH:
				$this->processParamSearch($param);
				break;
			default:
				$this->processParamNormal($param);
				break;
		}
	}

    /**
     * Обработка ординарных параметров соответствием рисков
     * @param FilterParam $param
     */
    public function processParamNormal(FilterParam $param){
		$slug = 'p2r'.$param->id;
		$this->query->innerJoin([$slug => 'api_erv_program2risk'], "aep.id = {$slug}.program_id AND {$slug}.risk_id = :{$slug}risk_id", [":{$slug}risk_id" => $param->risk_id]);
	}

    /**
     * Обработка параметра суммы
     * @param FilterParam $param
     */
    public function processParamSum(FilterParam $param){
		if ($param && $param->handler && $param->handler->variant) {
			/*
			$this->query->andWhere( [
				'between',
				'aep.summa',
				$param->handler->variant->from,
				$param->handler->variant->to
			] );
			*/
			$this->query->andWhere( [
				'>=',
				'aep.summa',
				$param->handler->variant->from
			] );
		}
	}

    /**
     * Обработка параметра беременности
     * @param FilterParam $param
     */
    public function processParamPregnancy(FilterParam $param){
		$slug = 'p2r'.$param->id;
		$this->query->innerJoin([$slug => 'api_erv_program2risk'], "aep.id = {$slug}.program_id AND {$slug}.risk_id = :risk_id", [':risk_id' => $param->risk_id]);
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
	}

    /**
     * Адаптация результата поиска в стандартное представление
     * @param Program $program
     * @param string $calcType
     *
     * @return ProgramResult
     */public function adapt(Program $program, $calcType = ApiModule::CALC_LOCAL){
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
		$model->cost          = $this->module->calcPrice($program, $this->form, $calcType);
		$model->phones        = $this->module->model->getPhonesAsArray();
		$model->calc          = $this->form;

		return $model;
	}

}