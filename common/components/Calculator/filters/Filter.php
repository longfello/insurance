<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 24.02.17
 * Time: 15:50
 */

namespace common\components\Calculator\filters;


use common\components\ApiModule;
use common\models\Api;
use common\models\ProgramResult;
use common\components\Calculator\forms\TravelForm;
use yii\base\Component;

/**
 * Class Filter нахождение предложений по заданным критериям по всем разрешенным API
 * @package common\components\Calculator\filters
 */
class Filter extends Component {
	/** @var TravelForm модель формы */
	public $form;

	/** @var Api[] разрешенные доступные API */
	public $apis;

    /**
     * инициализация - заполнение доступных API
     */
    public function init(){
		parent::init();
		if (count($this->form->apiIds)==0) {
			$this->apis = Api::find()->where(['enabled' => 1])->orderBy(['name' => SORT_ASC])->all();
		} else {
			$this->apis = Api::find()->where(['enabled' => 1])->andWhere(['id'=>$this->form->apiIds])->orderBy(['name' => SORT_ASC])->all();
		}
	}

	/**
	 * @return ProgramResult[] Поиск доступных программ по заданным критериям
     * @throws \Exception
	 */
	public function getPropositions($order_information = null){
		$results = [];
		foreach ($this->apis as $api){
			/** @var $api Api */
			$program = $api->search($this->form, $order_information);
			if ($program){
		        $results[] = $program;
			}
		}
  	return $results;
  }

    /**
     * @return bool|float возвращает минимальную стоимость полиса, false если ничего не найдено
     */
    public function getMinCost(){
	  $minCost = false;
	  foreach ($this->apis as $api){
		  /** @var $api Api */
		  if ($api->getModule()->has_local) {
			  $program = $api->search($this->form);
			  if ($program) {
				  if ($program->cost < $minCost || !$minCost) $minCost = $program->cost;
			  }
		  }
	  }
	  return $minCost;
  }
}