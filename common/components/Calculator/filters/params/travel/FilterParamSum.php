<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 10:11
 */

namespace common\components\Calculator\filters\params\travel;


use common\models\CostInterval;
use common\components\Calculator\models\travel\FilterParam;
use trntv\aceeditor\AceEditor;
use yii\base\Component;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;


/**
 * Фильтр суммы страхового полиса
 * Class FilterParamAccident
 * @package common\components\Calculator\filters\params\travel
 */
class FilterParamSum extends FilterParamPrototype {

    /** @inheritdoc */
	public $slug = self::SLUG_SUM;
    /** @inheritdoc */
    public $availableVariantVariables = [
        self::PARAM_SLUG_SIMPLE => 'ID интервала страховой суммы'
    ];

    /** @inheritdoc */
	public function getVariantsEditor(ActiveForm $form){
		$models = CostInterval::find()->orderBy(['from' => SORT_ASC])->all();
		$arr = [];
		foreach ($models as $one){
			$arr[] = "{$one->name} ({$one->from} - {$one->to})";
		}

		$html = Html::ul($arr);

		return Html::label('Интервалы страховых сумм') . $html;
	}

    /** @inheritdoc */
	public function render($form, $model){
		$models = CostInterval::find()->orderBy(['from' => SORT_ASC])->all();
		$variants = [];
		foreach ($models as $one){
			$variants[] = $one->name;
		}

		return "
        <div class=\"additional-options__label\">". $this->param->name ."
          <div class=\"helper__answer\"><div class=\"helper__icon\">?</div></div>
          <div class=\"helper__text\">".$this->param->getDescription()."</div>
        </div>
        <div class=\"progress__range\">
			<input name='param-{$this->param->id}' type='hidden' value='1'>
			<input name='param-{$this->param->id}-variant' value='".$this->getVariantValue()."' class='js-progress progress__input' data-variants='".implode(',', $variants)."'>
        </div>
";
	}

    /** @inheritdoc */
	public function getIsVariable(){
		return true;
	}

    /** @inheritdoc */
	public function loadVariant($params = []){
		$value = (isset($params[$this->param->id]))?$params[$this->param->id]:\Yii::$app->request->post('param-'.$this->param->id.'-variant', null);
		if ($value) {
			return CostInterval::find()->where(['OR', ['name' => $value], ['id' => $value]])->one();
		}
		return null;
	}

    /** @inheritdoc */
	public function getVariantValue(){
		return $this->variant?$this->variant->name:null;
	}

}