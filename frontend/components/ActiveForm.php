<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 22.02.17
 * Time: 14:33
 */

namespace frontend\components;


use yii\bootstrap\Html;

/**
 * Class ActiveForm Кустомная форма с поддержкой параметров сценария и типа формы
 * @package frontend\components
 */
class ActiveForm extends \kartik\form\ActiveForm {
    /**
     * @var
     */
    public $form_type;
    /**
     * @var bool
     */
    public $enableClientValidation = false;
    /**
     * @var bool
     */
    public $enableAjaxValidation = false;
    /**
     * @var
     */
    public $scenario;


    /**
     *
     */
    public function init(){
  	    parent::init();
        echo Html::hiddenInput('form_type', $this->form_type, ['class' => 'form_type']);
        echo Html::hiddenInput('form_scenario', $this->scenario);
  }
}