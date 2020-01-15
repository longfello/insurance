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

namespace common\modules\ApiVtb\components;


use common\components\ApiModule;
use common\models\ProgramResult;
use common\modules\ApiVtb\models\Price;
use common\modules\ApiVtb\Module;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\TravelForm;
use yii\base\Component;
use yii\db\ActiveQuery;

/**
 * Class ProgramSearch Подбор программы страхования
 * @package common\modules\ApiVtb\components
 */
class ProgramSearch extends Component {
	/**
	 * @var TravelForm модель формы параметров
	 */
	public $form;
	/**
	 * @var Module модуль Апи
	 */
	public $module;
	/**
	 * @var ActiveQuery поисковый запрос
	 */
	public $query;

	/**
     * Поиск программы страхования по заданным критериям
	 * @param bool $with_price use price filter
	 *
	 * @return null|ProgramResult
	 */
	public function findAll($with_price = true){

		if ($this->form->travellersCount>5) return null;

		$this->query = Price::find()->select('p.*', 'STRAIGHT_JOIN')->from(['p' => 'api_vtb_price'])->distinct();
		$this->query->leftJoin("api_vtb_program aep", 'aep.id = p.program_id');
		$this->query->leftJoin("api_vtb_amount a", 'a.id = p.amount_id');
		$this->query->leftJoin("api_vtb_period pe", 'pe.id = p.period_id');
		$this->query->where($this->form->dayCount. ' between pe.from AND pe.to');
		$this->query->orderBy(['a.amount' => ($with_price)?SORT_ASC:SORT_DESC, 'p.price' => SORT_ASC]);

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
     * Обработка прараметра страны
     */
    public function processCountries(){
		if ($this->form->countries) {
			$subQuery = Price::find()
						->alias('p')
			            ->select("p.program_id, count(distinct c.internal_id) as cnt")
						->leftJoin("api_vtb_region2country r2c", 'r2c.region_id = p.region_id')
						->leftJoin('api_vtb_country2dict c', 'c.api_id = r2c.country_id')
						->where(['c.internal_id' => $this->form->countries])
						->groupBy('p.program_id'); // , c.internal_id
			$this->query->innerJoin(['jCountry' => $subQuery], 'aep.id = jCountry.program_id AND jCountry.cnt = :cnt', [':cnt' => count($this->form->countries)]);
		}
	}

    /**
     * Обработка прараметров
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
			case FilterParamPrototype::SLUG_CANCEL:
				$this->processParamCancel($param);
				break;
			default:
				$this->processParamNormal($param);
				break;
		}
	}

    /**
     * Обработка ординарного параметра соответствия риска
     * @param FilterParam $param
     */
    public function processParamNormal(FilterParam $param){
		$slug = 'p2r'.$param->id;

		$subQuery = Price::find()
		                 ->alias('p')
		                 ->select("p.program_id", 'STRAIGHT_JOIN')
		                 ->leftJoin("api_vtb_price2risk p2r", 'p2r.price_id = p.id')
		                 ->leftJoin('api_vtb_risk2internal r2i', 'r2i.risk_id = p2r.risk_id')
		                 ->where(['r2i.internal_id' => $param->risk_id])
		                 ->groupBy('p.program_id');

		$this->query->innerJoin([$slug => $subQuery], 'aep.id = '.$slug.'.program_id');
	}

    /**
     * Обработка прараметра суммы
     * @param FilterParam $param
     */
    public function processParamSum(FilterParam $param){
		if ($param && $param->handler && $param->handler->variant) {
			$subQuery = Price::find()
			                 ->alias( 'p' )
			                 ->select( "p.id", 'STRAIGHT_JOIN' )
			                 ->leftJoin( "api_vtb_amount a", 'a.id = p.amount_id' )
			                 /*->where( [
				                 'between',
				                 'a.amount',
				                 $param->handler->variant->from,
				                 $param->handler->variant->to
			                 ] )
			                 */
							 ->where( [
								 '>=',
								 'a.amount',
								 $param->handler->variant->from
							 ] )
			                 ->groupBy( 'p.id' );
			$this->query->innerJoin( [ 'amount' => $subQuery ], 'p.id = amount.id' );
		}
	}

    /**
     * Обработка прараметра беременности
     * @param FilterParam $param
     */
    public function processParamPregnancy(FilterParam $param){
		$slug = 'p2r'.$param->id;

		$subQuery = Price::find()
			->alias('p')
			->select("p.program_id", 'STRAIGHT_JOIN')
			->leftJoin("api_vtb_price2risk p2r", 'p2r.price_id = p.id')
			->leftJoin('api_vtb_risk2internal r2i', 'r2i.risk_id = p2r.risk_id')
			->where(['r2i.internal_id' => $param->risk_id])
			->groupBy('p.program_id');

		$this->query->innerJoin([$slug => $subQuery], 'aep.id = '.$slug.'.program_id');
		$this->query->andWhere('aep.pregnant_week>='.$param->handler->variant);
	}

    /**
     * Обработка прараметра франшизы
     * @param FilterParam $param
     */
    public function processParamNotFranchise(FilterParam $param) {
	}

    /**
     * Обработка прараметра репатриации
     * @param FilterParam $param
     */
    public function processParamRepatriation(FilterParam $param) {
	}

    /**
     * Обработка прараметра поисковых работ
     * @param FilterParam $param
     */
    public function processParamSearch(FilterParam $param) {
		$this->query->andWhere('0=1');
	}

    /**
     * Обработка прараметра отмены поездки
     * @param FilterParam $param
     */
    public function processParamCancel(FilterParam $param) {
		$sick_list = $param->handler->variant['sick-list'];
		if ($sick_list) {
			$this->query->andWhere('0=1');
		}
	}

    /**
     * Адаптация результата поиска в стандартное представление
     * @param Price $price
     * @param string $calcType
     *
     * @return ProgramResult
     */
    public function adapt(Price $price, $calcType = ApiModule::CALC_LOCAL){
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