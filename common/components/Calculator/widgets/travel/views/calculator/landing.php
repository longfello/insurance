<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\components\Calculator\forms\TravelForm
 *
 */

$type = \common\models\InsuranceType::findOne(['slug' => \common\components\Calculator\forms\TravelForm::SLUG_TRAVEL]);
?>

<div class="insurance-find__type insurance-find__type_landing" id="calc-<?= $type->slug ?>">
    <div class="insurance-find-form insurance-find-form_landing insurance-find-form_<?= $type->slug ?>">
<?php

$form = \frontend\components\ActiveForm::begin([
    'action' => \yii\helpers\Url::to(['page/view', 'slug' => $type->resultPage->slug]),
//	'action' => '/calc.html',
    'method' => 'post',
    'form_type' => \common\components\Calculator\forms\prototype::SLUG_TRAVEL,
    'enableClientValidation' => true,
    'scenario' => 'home',
    'options' => [
        'class'  => 'insurance-find__body',
    ]
]);
?>
        <div class="filter insurance-find__filter">
            <div href="#" class="insurance-find__title insurance-find__title_landing title">Страхование <span class="insurance-find__title insurance-find__title_landing insurance-find__title_landing_big">путешественников</span></div>
            <?= \yii\bootstrap\Html::errorSummary($model); ?>
            <div class="filter__country">
                <div class="filter__label filter__label_country title title_size_s">Страна
                </div>
                <div class="filter__select-help">
                    <?=$form->field($model, 'countries')->dropDownList( \yii\helpers\ArrayHelper::map(
                        \common\models\GeoCountry::find()->orderBy(['type' => SORT_DESC, 'name' => SORT_ASC])->all(),
                        'id',
                        'name'
                    ), [
                        'class' => "js-select select",
                        'style' => "width:100%",
                        'multiple' => "multiple"
                    ]);?>
                </div>
            </div>
            <div class="filter__dates 111">
                <div class="group-input">
                    <div class="filter__label title title_size_s">Туда</div>
                    <div class="date-field filter__input">
                        <?= $form->field($model, 'dateFrom')->textInput([
                            'class' => 'input input_color_black input_size_m js-datepicker',
                            'autocomplete' => 'off',
                            'readonly'     => 'readonly'
                        ]); ?>
                        <div class="date-field__icon">
                            <svg class="icon icon_calendar ">
                                <use xlink:href="#icon-calendar"></use>
                            </svg>
                        </div>
                        <?= \yii\bootstrap\Html::error($model, 'dateFrom'); ?>
                    </div>
                </div>
                <div class="group-input">
                    <div class="filter__label title title_size_s">Обратно</div>
                    <div class="date-field filter__input">
                        <?= $form->field($model, 'dateTo')->textInput([
                            'class' => 'input input_color_black input_size_m js-datepicker',
                            'autocomplete' => 'off',
                            'readonly'     => 'readonly'
                        ]); ?>
                        <div class="date-field__icon">
                            <svg class="icon icon_calendar ">
                                <use xlink:href="#icon-calendar"></use>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="filter__submit">
                <button type="submit" class="button button_color_green button_size_l button_uppercase">Найти страховку</button>
            </div>
        </div>
<?php \frontend\components\ActiveForm::end() ?>
    </div>
</div>