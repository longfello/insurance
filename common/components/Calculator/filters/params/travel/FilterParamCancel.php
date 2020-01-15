<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 10:11
 */

namespace common\components\Calculator\filters\params\travel;


use common\components\Calculator\forms\TravelForm;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;


/**
 * Фильтр отмены поездки
 * Class FilterParamAccident
 * @package common\components\Calculator\filters\params\travel
 */
class FilterParamCancel extends FilterParamPrototype {

    /** @inheritdoc */
	public $slug = self::SLUG_CANCEL;

    /** @inheritdoc */
	public function getVariantsEditor(ActiveForm $form){
		return $form->field($this->param, 'variants', ['template' => ''])->hiddenInput();
	}

	/** @inheritdoc */
    public $availableVariantVariables = [
        'amount' => 'Страховая сумма',
        'sick-list' => 'Больничный (0/1)',
    ];

    /** @inheritdoc */
	public function render($form, $model){
		/** @var $form \kartik\form\ActiveForm */
		/** @var $model TravelForm */
		$variantHtml = '';
		$visible = $this->checked?" style='display:block;' ":"";

		$variantHtml .= "
        <div class=\"cancel-progress escape-travel\" {$visible}>
          <div class=\"options__item\">
              <div class=\"options__label\">Стоимость тура <span class=\"title_size_l\">(на одного застрахованного в Евро)</span></div>
              <div class=\"escape_sum-block\">
                    <input class=\"input input_size_m input_escape_sum form-control\"  value='{$this->variant['amount']}' name='param-".$this->param->id."-variant[amount]'>
                    <span class=\"euro-sign\">-- €</span>
              </div>
              <div class=\"example-coast\">
                  <span class=\"title_size_l\">Например:                  
                      <span class=\"example-coast__item\"><span class=\"example-coast__value\">500</span><span class=\"currensy\">€</span></span>,
                      <span class=\"example-coast__item\"><span class=\"example-coast__value\">1000</span><span class=\"currensy\">€</span></span>,
                      <span class=\"example-coast__item\"><span class=\"example-coast__value\">2000</span><span class=\"currensy\">€</span></span>
                  </span>
              </div>
          </div>
          <div class=\"checkbox-list__item\">
              <label class=\"checkbox\">
              	". Html::checkbox('param-'.$this->param->id.'-variant[sick-list]', $this->variant['sick-list'], ['class' => 'checkbox__input']) ."
                  <span class=\"checkbox__icon\"></span><span class=\"checkbox__label\">По больничному листу</span>
              </label>
          </div>
        </div>
		";

		return "
          <div class=\"checkbox-list__item checkbox-list__item_escape-travel\">
            <label class=\"checkbox\">
              ". Html::checkbox('param-'.$this->param->id, $this->checked, ['class' => 'checkbox__input']) ."
              <span class=\"checkbox__icon\"></span><span class=\"checkbox__label\">".$this->param->name."</span>
            </label>
            {$variantHtml}
        </div>
";
	}

    /** @inheritdoc */
	public function loadVariant($params = []){
		if ($data = (isset($params[$this->param->id]))?$params[$this->param->id]:\Yii::$app->request->post('param-'.$this->param->id.'-variant', null)){
			if (!is_array($data)){
				$data = $this->decode($data);
			}
			$data['amount'] = min($data['amount'], 5000);
			return $data;
		} else return [
			'amount' => 0,
			'sick-list' => 0,
		];
	}

    /** @inheritdoc */
	public function getVariantValue(){
		$amount = ($this->variant && isset($this->variant['amount']))?$this->variant['amount']:0;
		$amount = min($amount, 5000);
		$sick_list = ($this->variant && isset($this->variant['sick-list']))?$this->variant['sick-list']:0;
		return $this->encode([
			'amount' => $amount,
			'sick-list' => $sick_list,
		]);
	}

    /** @inheritdoc */
	public function setVariant($value){
		$amount = isset($value['amount'])?$value['amount']:0;
		$sick_list = isset($value['sick-list'])?$value['sick-list']:0;
		$this->variant = [
			'amount' => $amount,
			'sick-list' => $sick_list,
		];
		return true;
	}
}