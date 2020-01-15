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
use common\components\Calculator\forms\TravelForm;
use trntv\aceeditor\AceEditor;
use yii\base\Component;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;


/**
 * Фильтр беременности
 * Class FilterParamAccident
 * @package common\components\Calculator\filters\params\travel
 */
class FilterParamPregnancy extends FilterParamPrototype {

    /** @inheritdoc */
	public $slug = self::SLUG_PREGNANCY;

    /** @inheritdoc */
    public $availableVariantVariables = [
        self::PARAM_SLUG_SIMPLE => 'Количество недель беременности'
    ];

    /** @inheritdoc */
	public function getVariantsEditor(ActiveForm $form){
		return $form->field($this->param, 'variants')->widget(
			AceEditor::className(),
			[
				'mode' => 'json'
			]
		)->hint("Варианты вводятся в формате:<br> {<br>&nbsp;&nbsp;\"Количество недель\" : [12, 24, 31]<br>}<br><br>Если ничего не введено, варианты будут импортированы из риска");
	}

    /** @inheritdoc */
	public function render($form, $model){
		/** @var $form \kartik\form\ActiveForm */
		/** @var $model TravelForm */

		$params = [];
		if ($this->param->variants){
			$params = (array)json_decode($this->param->variants);
		} elseif ($this->param->risk && $this->param->risk->params) {
			$params = (array)json_decode($this->param->risk->params);
		}

		$variantHtml = '';
		foreach($params as $name => $variants){

			$visible = $this->checked?" style='display:block;' ":"";

			$variantHtml .= "
            <div class=\"pregnant-progress\" {$visible}>
              <div class=\"pregnant-progress__title title\">{$name}:</div>
              <div class=\"progress__range\">
                <input name='param-".$this->param->id."-variant' class=\"js-progress progress__input\" value='".$this->getVariantValue()."' data-variants='".implode(',', $variants)."'>
              </div>
            </div>
			";
		}

		return "
          <div class=\"checkbox-list__item checkbox-list__item_pregnant\">
            <label class=\"checkbox\">
              ". Html::checkbox('param-'.$this->param->id, $this->checked, ['class' => 'checkbox__input']) ."
              <span class=\"checkbox__icon\"></span><span class=\"checkbox__label\">".$this->param->name."</span>
            </label>
            {$variantHtml}
          </div>";
	}

    /** @inheritdoc */
	public function getVariantValue() {
		if ($this->variant) {
			return $this->variant;
		} else {
			$params = [];
			if ($this->param->variants){
				$params = (array)json_decode($this->param->variants);
			} elseif ($this->param->risk && $this->param->risk->params) {
				$params = (array)json_decode($this->param->risk->params);
			}

			foreach($params as $name => $variants){
				if ($name=='Количество недель') {
					return min($variants);
				}
			}
		}
	}
}