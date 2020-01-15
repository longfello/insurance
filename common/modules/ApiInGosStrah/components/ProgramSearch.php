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

namespace common\modules\ApiInGosStrah\components;


use common\components\ApiModule;
use common\models\ProgramResult;
use common\modules\ApiAlphaStrah\models\Price;
use common\modules\ApiAlphaStrah\Module;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\TravelForm;
use yii\base\Component;
use yii\db\ActiveQuery;

/**
 * Class ProgramSearch поиск программ страхования
 * @package common\modules\ApiInGosStrah\components
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
     * Поиск программ по заданным критериям
     * @return ProgramResult|null
     */
    public function findAll(){
		$this->query = Price::find()->select('p.*')->from(['p' => 'api_alpha_price'])->distinct();
		$this->query->leftJoin("api_alpha_insurance_programm aep", 'aep.insuranceProgrammID = p.program_id');
		$this->query->leftJoin("api_alpha_amount a", 'a.id = p.amount_id');
		$this->query->orderBy(['a.amount' => SORT_ASC]);

		$this->processCountries();

		foreach($this->form->params as $param){
			if ($param->handler->checked) {
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
		} else return null;
	}

    /**
     * Обработка параметра страны
     */
    public function processCountries(){
		if ($this->form->countries) {
			$subQuery = Price::find()
						->alias('p')
			            ->select("p.program_id, count(distinct c.internal_id) as cnt")
						->leftJoin("api_alpha_region2country r2c", 'r2c.region_id = p.region_id')
						->leftJoin('api_alpha_country2dict c', 'c.api_id = r2c.country_id')
						->where(['c.internal_id' => $this->form->countries])
						->groupBy('p.program_id, c.internal_id');
			$this->query->innerJoin(['jCountry' => $subQuery], 'aep.insuranceProgrammID = jCountry.program_id AND jCountry.cnt = :cnt', [':cnt' => count($this->form->countries)]);
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
			default:
				$this->processParamNormal($param);
				break;
		}
	}

    /**
     * Обработка ординарных параметров соответствием риску
     * @param FilterParam $param
     */
    public function processParamNormal(FilterParam $param){
		$slug = 'p2r'.$param->id;

		$subQuery = Price::find()
		                 ->alias('p')
		                 ->select("p.program_id")
		                 ->leftJoin("api_alpha_risk2program r2p", 'r2p.program_id = p.program_id')
		                 ->leftJoin('api_alpha_risk r2i', 'r2i.riskID = r2p.risk_id')
		                 ->where("CONCAT(',',r2i.parent_id,',') LIKE '%,".$param->risk_id.",%'")
		                 ->groupBy('p.program_id');

		$this->query->innerJoin([$slug => $subQuery], 'aep.insuranceProgrammID = '.$slug.'.program_id');
	}

    /**
     * Обработка параметра страховой суммы
     * @param FilterParam $param
     */
    public function processParamSum(FilterParam $param){
		$subQuery = Price::find()
		                 ->alias('p')
		                 ->select("p.program_id")
		                 ->leftJoin("api_alpha_amount a", 'a.id = p.amount_id')
		                 ->where(['between', 'a.amount', $param->handler->variant->from, $param->handler->variant->to])
		                 ->groupBy('p.program_id');
		$this->query->innerJoin(['amount' => $subQuery], 'aep.insuranceProgrammID = amount.program_id');
	}

    /**
     * Обработка параметра беременности
     * @param FilterParam $param
     */
    public function processParamPregnancy(FilterParam $param){
		$this->query->andWhere('0=1');
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
		$model->risks         = $price->getRisksAsArray();
		$model->actions       = $this->module->model->actions;
		$model->cost          = $this->module->calcPrice($price, $this->form, $calcType);
		$model->phones        = $this->module->model->getPhonesAsArray();
		$model->calc          = $this->form;

		return $model;
	}

}