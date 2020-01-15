<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\components;


use common\components\ApiModule;
use common\models\ProgramResult;
use common\modules\ApiLiberty\Module;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\TravelForm;
use common\modules\ApiLiberty\models\Product;
use common\modules\ApiLiberty\models\Program;
use yii\base\Component;
use yii\db\ActiveQuery;
use yii\base\Exception;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * Class ProgramSearch Подбор программ страхования
 * @package common\modules\ApiLiberty\components
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
     * Поиск программ по заданым критериям
     * @param bool $with_price
     *
     * @return ProgramResult|null
     * @throws Exception
     */
    public function findAll($with_price = true){
		if ($this->form->countries) {
			$countries_query = (new \yii\db\Query())->select('t.id_area, t.name')->from(['t' => 'api_liberty_territory']);
			$countries_query->innerJoin("api_liberty_territory2dict t2d", 't2d.id_area=t.id_area');
			$countries_query->where(['t2d.internal_id' => $this->form->countries]);

			$countries = [];
			foreach ($countries_query->all() as $country_row) {
				$countries[$country_row['id_area']] = $country_row['name'];
			}

			$this->query = (new \yii\db\Query())->select('p.*, r.riskId, r.riskName, s.id as summ_id, s.amount, count(DISTINCT t2p.id_area) as kol_areas')->from(['p' => 'api_liberty_product']);
			$this->query->innerJoin("api_liberty_risk2product r2p", 'r2p.productId = p.productId');
			$this->query->innerJoin("api_liberty_risk r", 'r.riskId=r2p.riskId AND r.main=1');
			$this->query->innerJoin("api_liberty_territory2product t2p", 't2p.productId = p.productId');
			$this->query->innerJoin("api_liberty_summ s", 's.productId = p.productId AND s.riskId=r.riskId AND s.countryId=t2p.id_area');
			$this->query->where(['t2p.id_area' => array_keys($countries)]);
			$this->query->andHaving(['kol_areas' => count($countries)]);
			$this->query->groupBy(['s.amount', 'p.productId']);
			$this->query->orderBy(['s.amount' => ($with_price)?SORT_ASC:SORT_DESC, 'p.productId'=>SORT_ASC]);


			$summ_param = false;
			$cancel_param = false;
			$sport_param = false;
			foreach($this->form->params as $param){
				if ($param->handler->checked) {
					if ($param->handler->slug == FilterParamPrototype::SLUG_SUM) {
						$summ_param = $param;
					} elseif ($param->handler->slug == FilterParamPrototype::SLUG_CANCEL && $param->handler->variant['amount']>0) {
						$cancel_param = $param;
					} elseif ($param->handler->slug == FilterParamPrototype::SLUG_SPORT) {
						$sport_param = $param;
					}
				}
			}

			$risk_ids = [];
            foreach($this->form->params as $param){
				if ($param->handler->checked && ($with_price || $param->handler->slug!=FilterParamPrototype::SLUG_SUM)) {
					$risk_ids[] = $param->risk_id;
                    $this->processParam($param, array_keys($countries), $summ_param->handler->variant->id);
                }
            }

/*			echo $this->query->createCommand()->rawSql;
			echo "<br/>";*/

			$result = $this->query->one();
			if ($result) {
				$risks = [];
				if ($summ_param) {
					$risks_query = (new \yii\db\Query())->select('r.riskId, r.riskName, r.description, s.amount')->from(['r' => 'api_liberty_risk']);
					$risks_query->innerJoin("api_liberty_risk2internal r2i", "r2i.riskId = r.riskId");
					$risks_query->innerJoin("api_liberty_risk2product r2p", "r2p.riskId=r.riskId AND r2p.productId = ".$result['productId']);
					$risks_query->innerJoin("api_liberty_territory2product t2p", " t2p.productId = ".$result['productId']);
					$risks_query->innerJoin("api_liberty_summ s", "s.productId = ".$result['productId']." AND s.riskId = r.riskId AND s.countryId = t2p.id_area");
					$risks_query->innerJoin("api_liberty_summ2interval s2i", "s2i.summ_id = s.id");
					$risks_query->Where(['t2p.id_area' => array_keys($countries)]);
					$risks_query->andWhere(['r2i.internal_id'=>$risk_ids, 's2i.cost_id'=>$summ_param->handler->variant->id]);

					foreach ($risks_query->all() as $risk_row) {
						$risks[$risk_row['riskId']] = [
							'name' => $risk_row['riskName'],
							'description' => $risk_row['description'],
							'amount' =>$risk_row['amount']
						];
					}
				}

				if ($cancel_param) {
					$risk_cancel_query = (new \yii\db\Query())->select('r.riskId, r.riskName')->from(['r' => 'api_liberty_risk']);
					$risk_cancel_query->innerJoin("api_liberty_risk2internal r2i", "r2i.riskId = r.riskId");
					$risk_cancel_query->innerJoin("api_liberty_risk2product r2p", "r2p.riskId=r.riskId AND r2p.productId = ".$result['productId']);
					$risk_cancel_query->innerJoin("api_liberty_territory2product t2p", " t2p.productId = ".$result['productId']);
				    $risk_cancel_query->Where(['t2p.id_area' => array_keys($countries)]);
					$risk_cancel_query->andWhere(['r2i.internal_id'=>$cancel_param->risk_id]);

					if($risk_cancel_result = $risk_cancel_query->one()){
						$risks[$risk_cancel_result['riskId']] = [
							'name' => $risk_cancel_result['riskName'],
							'amount' =>$cancel_param->handler->variant['amount']
						];
					}
				}

				$medical_option = 0;
				if ($sport_param) {
					$medical_option_query = (new \yii\db\Query())->select('o.id')->from(['o' => 'api_liberty_occupation']);
					$medical_option_query->innerJoin("api_liberty_occupation2product o2p", "o2p.id = o.id AND o2p.productId=".$result['productId']);
					$medical_option_query->Where(['o.is_sport' => 1]);

					if($medical_option_result = $medical_option_query->one()){
						$medical_option = $medical_option_result['id'];
					}
				}

				/** @var $program Program */
				$program = new Program();
				$program->load((array)$result, '');
				$program->countries = $countries;
				$program->risks = $risks;
				$program->medical_option = $medical_option;
				if (!$program->save()) {
					throw new Exception(strip_tags(Html::errorSummary($program)), 500);
				}

				if (in_array($this->form->scenario, [TravelForm::SCENARIO_PREPAY, TravelForm::SCENARIO_PAYER, TravelForm::SCENARIO_PAY])) {
					return $this->adapt($program, ApiModule::CALC_API);
				} else {
					return $this->adapt($program);
				}
			} elseif ($with_price) {
				return $this->findAll(false);
			} else return null;
		} else return null;
	}


    /**
     * Обработка параметров фильтра
     * @param FilterParam $param
     * @param $countries
     * @param int $cost_interval_id
     */
    public function processParam(FilterParam $param, $countries, $cost_interval_id = 0){
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
				$this->processParamNormal($param, $countries, $cost_interval_id);
				break;
		}
	}

    /**
     * Обработка параметра суммы
     * @param FilterParam $param
     */
    public function processParamSum(FilterParam $param){
		$this->query->andWhere(['>=', 's.amount', $param->handler->variant->from]);
	}

    /**
     * Обработка ординарных параметров соответствия рискам
     * @param FilterParam $param
     * @param $countries
     * @param $cost_interval_id
     */
    public function processParamNormal(FilterParam $param, $countries, $cost_interval_id){
		$slug = 'p2r'.$param->id;

		$subQuery = Product::find()
		                 ->alias('p')
		                 ->select("p.productId")
		                 ->innerJoin("api_liberty_risk2product r2p", 'r2p.productId = p.productId')
		                 ->innerJoin('api_liberty_risk2internal r2i', 'r2i.riskId = r2p.riskId')
			             ->innerJoin('api_liberty_risk r', 'r.riskId = r2p.riskId')
					     ->leftJoin("api_liberty_summ s", "s.productId = p.productId AND s.riskId = r2p.riskId")
					     ->leftJoin("api_liberty_summ2interval s2i", "s2i.summ_id = s.id")
		                 ->where(['r2i.internal_id'=>$param->risk_id])
			             ->andWhere(['or', ['s.countryId' => $countries], ['r.main'=>1]])
			             ->andWhere(['or', ['s2i.cost_id' => $cost_interval_id], ['r.main'=>1]])
		                 ->groupBy('p.productId');

		$this->query->innerJoin([$slug => $subQuery], 'p.productId = '.$slug.'.productId');
	}

    /**
     * Обработка параметра занятия спортом
     * @param FilterParam $param
     */
    public function processParamSport(FilterParam $param) {
		$slug = 'p2r'.$param->id;

		$subQuery = Product::find()
			->alias('p')
			->select("p.productId")
			->innerJoin("api_liberty_occupation2product o2p", 'o2p.productId = p.productId')
			->innerJoin('api_liberty_occupation o', 'o.id = o2p.id')
			->where(['o.is_sport' =>1])
			->groupBy('p.productId');

		$this->query->innerJoin([$slug => $subQuery], 'p.productId = '.$slug.'.productId');
	}

    /**
     * Обработка параметра беременности
     * @param FilterParam $param
     */
    public function processParamPregnancy(FilterParam $param){
		$this->query->andWhere('0=1');
	}

    /**
     * Обработка параметра поисковх работ
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
		$sick_list = $param->handler->variant['sick-list'];
		if ($sick_list) {
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
    public function adapt(Program $program, $calcType = ApiModule::CALC_LOCAL) {
		//$price = $this->module->calcPrice($program, $this->form, $calcType);

		//if ($price>0) {
		$model = new ProgramResult();

		$model->api_id        = $this->module->model->id;
		$model->program_id    = $program->id;
		$model->rate_expert   = $this->module->model->rate_expert;
		$model->rate_asn      = $this->module->model->rate_asn;
		$model->thumbnail_url = $this->module->model->thumbnail_base_url.'/'.$this->module->model->thumbnail_path;
		$model->rule_url      = $program->product->rule_base_url.'/'.$program->product->rule_path;
		$model->police_url    = $program->product->police_base_url.'/'.$program->product->police_path;
		$model->risks         = $program->getRisksAsArray();
		$model->actions       = $this->module->model->actions;
        $model->cost          = ($this->form->forceRemoteCalc) ? $this->module->calcPrice($program, $this->form, $calcType) : 0;
		$model->phones        = $this->module->model->getPhonesAsArray();
		$model->calc          = $this->form;

		return $model;
		//} else return null;
	}
}