<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\components;


use common\components\ApiModule;
use common\models\ProgramResult;
use common\modules\ApiTinkoff\models\Area;
use common\modules\ApiTinkoff\models\Country;
use common\modules\ApiTinkoff\models\Price;
use common\modules\ApiTinkoff\models\Price2Risk;
use common\modules\ApiTinkoff\models\Risk;
use common\modules\ApiTinkoff\Module;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\TravelForm;
use yii\base\Component;
use yii\db\ActiveQuery;

/**
 * Class ProgramSearch Подбор программ страхования
 * @package common\modules\ApiTinkoff\components
 */
class ProgramSearch extends Component {
	/**
	 * @var TravelForm модель формы параметров
	 */
	public $form;
	/**
	 * @var Module модуль АПИ
	 */
	public $module;
	/**
	 * @var ActiveQuery поисковый запрос
	 */
	public $query;

    /**
     * Поиск программы страхования
     * @param bool $with_price
     *
     * @return ProgramResult|null
     */
    public function findAll($with_price = true){

		if ($this->form->travellersCount>5) return null;

		$this->query = Price::find()->select('price.*')->from(['price' => 'api_tinkoff_price']);
		$this->query->InnerJoin("api_tinkoff_product product", 'product.id = price.product_id');
		$this->query->orderBy(['price.TravelMedicineLimit' => ($with_price)?SORT_ASC:SORT_DESC]);

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
     * Обработка территорий
     * @return array|Area[]|Risk[]|\yii\db\ActiveRecord[]
     */
    public function findAreas() {
		$areas_query = Area::find()->select('Value')->from(['a' => 'api_tinkoff_area']);
		$areas_query->InnerJoin("api_tinkoff_area2dict a2d", 'a2d.area_id = a.id');
		$areas_query->where(['a.enabled'=>1])->andWhere(['a2d.internal_id'=>$this->form->countries]);

		return $areas_query->all();
	}

    /**
     * Обработка стран
     * @return array|Area[]|Risk[]|\yii\db\ActiveRecord[]
     */
    public function findCountries() {
		$areas_query = Country::find()->select('c.Value')->from(['c' => 'api_tinkoff_country']);
		$areas_query->InnerJoin("api_tinkoff_country2dict c2d", 'c2d.country_id = c.id');
		$areas_query->where(['c.enabled'=>1])->andWhere(['c2d.internal_id'=>$this->form->countries]);

		return $areas_query->all();
	}

    /**
     * Обработка рисков
     * @param Price $price
     *
     * @return array
     */
    public function findCoverages(Price $price) {
		$res = [];

		foreach($this->form->params as $param) {
			if ($param->handler->checked) {
				$risks_query = Risk::find()
					->alias('r')
					->select("r.*")
					->innerJoin('api_tinkoff_risk2internal r2i', 'r2i.risk_id = r.id')
					->where(['r.parent_id' => 0])
					->andWhere(['!=', 'r.Code', 'TravelMedicine'])
					->andWhere(['r2i.internal_id' => $param->risk_id]);

				$risks = $risks_query->all();
				foreach ($risks as $one_risk) {
					$subrisks_query = Risk::find()
						->alias('r')
						->select("r.*")
						->innerJoin("api_tinkoff_price2risk p2r", 'p2r.risk_id = r.id')
						->where(['r.parent_id' => $one_risk->id]);

					$subrisks = $subrisks_query->all();
					if (count($subrisks)==0 && $one_risk->Type=='BOOLEAN') {
						$res[] = ['Code' => $one_risk->Code];
					} else {
						$subres = [];
						foreach ($subrisks as $subrisk) {
							if ($subrisk->Type=='DECIMAL') {
								$p2r = Price2Risk::find()->where(['risk_id'=>$subrisk->id, 'price_id'=>$price->id])->one();
								if ($p2r) $subres[] = ['Code'=> $subrisk->Code, 'Value'=>$p2r->amount];
							}
						}

						$res[] = ['Code' => $one_risk->Code, 'ValueInfo' => $subres];
					}
				}
			}
		}
		return $res;
	}

    /**
     * Обработка стран
     */
    public function processCountries(){
		if ($this->form->countries) {
			$subQuery = Price::find()
						->alias('p')
			            ->select("p.id, count(DISTINCT c2d.internal_id) + count(DISTINCT a2d.internal_id) as cnt")
				        ->innerJoin('api_tinkoff_price2country p2c', 'p2c.price_id = p.id')
						->leftJoin('api_tinkoff_country2dict c2d', ['and', ['c2d.country_id' => new \yii\db\Expression('p2c.country_id')], ['c2d.internal_id' => $this->form->countries]])
						->innerJoin('api_tinkoff_price2area p2a', 'p2a.price_id = p.id')
						->leftJoin('api_tinkoff_area2dict a2d', ['and', ['a2d.area_id' => new \yii\db\Expression('p2a.area_id')], ['a2d.internal_id' => $this->form->countries]])
						->groupBy('p.id');
			$this->query->innerJoin(['jCountry' => $subQuery], 'price.id = jCountry.id AND jCountry.cnt = :cnt', [':cnt' => count($this->form->countries)]);
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
			case FilterParamPrototype::SLUG_SEARCH:
				$this->processParamSearch($param);
				break;
			case FilterParamPrototype::SLUG_CANCEL:
				$this->processParamCancel($param);
				break;
			case FilterParamPrototype::SLUG_SPORT:
				$this->processParamSport($param);
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
		$this->query->andWhere(['>=', 'price.TravelMedicineLimit', $param->handler->variant->from]);
	}

    /**
     * Обработка ординарных параметров соответствия риску
     * @param FilterParam $param
     */
    public function processParamNormal(FilterParam $param){

		$slug = 'p2r'.$param->id;

		$subQuery = Price::find()
			->alias('p')
			->select("p.id", 'STRAIGHT_JOIN')
			->leftJoin("api_tinkoff_price2risk p2r", 'p2r.price_id = p.id')
			->leftJoin('api_tinkoff_risk2internal r2i', 'r2i.risk_id = p2r.risk_id')
			->where(['r2i.internal_id' => $param->risk_id])
			->groupBy('p.id');

		$this->query->innerJoin([$slug => $subQuery], 'price.id = '.$slug.'.id');
	}

    /**
     * Обработка параметра занятия спортом
     * @param FilterParam $param
     */
    public function processParamSport(FilterParam $param) {

	}

    /**
     * Обработка параметра беременности
     * @param FilterParam $param
     */
    public function processParamPregnancy(FilterParam $param){
		$this->query->andWhere('0=1');
	}

    /**
     * Обработка параметра поисковых работ
     * @param FilterParam $param
     */
    public function processParamSearch(FilterParam $param){
		$this->query->andWhere('0=1');
	}

    /**
     * Обработка параметра отмены поездки
     * @param FilterParam $param
     */
    public function processParamCancel(FilterParam $param) {
		$date_from = \DateTime::createFromFormat('d.m.Y', trim($this->form->dateFrom));
		$now =new \DateTime("now");
		$diff = $now->diff($date_from);
		if ($diff->format('%d')>=5) {
			$amount = $param->handler->variant['amount'];
			$sick_list = $param->handler->variant['sick-list'];
			if ($amount > 1000 || $sick_list) {
				$this->query->andWhere('0=1');
			}
		} else {
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
		//$cost = $this->module->calcPrice($price, $this->form, $calcType);

		//if ($price>0) {
		$model = new ProgramResult();

		$model->api_id        = $this->module->model->id;
		$model->program_id    = $price->id;
		$model->rate_expert   = $this->module->model->rate_expert;
		$model->rate_asn      = $this->module->model->rate_asn;
		$model->thumbnail_url = $this->module->model->thumbnail_base_url.'/'.$this->module->model->thumbnail_path;
		$model->rule_url      = $price->product->rule_base_url.'/'.$price->product->rule_path;
		$model->police_url    = $price->product->police_base_url.'/'.$price->product->police_path;
		$model->risks         = $price->getRisksAsArray($this->form);
		$model->actions       = $this->module->model->actions;
        $model->cost          = ($this->form->forceRemoteCalc) ? $this->module->calcPrice($price, $this->form, $calcType) :0;
		$model->phones        = $this->module->model->getPhonesAsArray();
		$model->calc          = $this->form;

		return $model;
		//} else return null;
	}

}