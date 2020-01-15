<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\components\Calculator\forms\TravelForm
 *
 */

$type = \common\models\InsuranceType::findOne(['slug' => \common\components\Calculator\forms\TravelForm::SLUG_TRAVEL]);
?>
        <?php

        $form = \frontend\components\ActiveForm::begin([
            'action' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(['page/view', 'slug' => $type->resultPage->slug], true),
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
            <?= \yii\bootstrap\Html::errorSummary($model); ?>

            <div class="filter insurance-find__filter">
                <div class="filter__title">Страхование путешественников</div>
                <div class="filter__country">
                    <div class="filter__label">Страна</div>
                    <?=$form->field($model, 'countries')->dropDownList( \yii\helpers\ArrayHelper::map(
                        \common\models\GeoCountry::find()->orderBy(['type' => SORT_DESC, 'name' => SORT_ASC])->all(),
                        'id',
                        'name'
                    ), [
                        'class' => "js-select filter__select",
                        'multiple' => "multiple"
                    ]);?>
                    <?= \yii\bootstrap\Html::error($model, 'countries'); ?>

                    <?php
                     $popular = \common\models\GeoCountry::find()->where(['is_popular'=>1])->orderBy(['type' => SORT_DESC, 'name' => SORT_ASC])->all();
                     if ($popular) {?>
                        <div class="filter-popular">
                            <ul class="filter-popular__list">
                                <li class="filter-popular__title">Популярные: </li>
                                <?php foreach ($popular as $popular_country) {?>
                                    <li class="filter-popular__item"><a class="filter-popular__link" data-id="<?= $popular_country->id; ?>"><?= $popular_country->name; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                     <?php } ?>
                </div>
                <div class="filter__dates">
                    <div class="filter-input filter-input_datefrom">
                        <div class="filter__label filter__label_datefrom">Туда</div>
                        <?= $form->field($model, 'dateFrom')->textInput([
                            'class' => 'date-input date-input_datefrom js-datepicker',
                            'autocomplete' => 'off',
                            'readonly'     => 'readonly'
                        ]); ?>
                        <?= \yii\bootstrap\Html::error($model, 'dateFrom'); ?>
                    </div>
                    <div class="days-counter" id="travelform_days_wrapper">
                        <span class="days-counter__quantity" style="display: none">0</span>
                        <span class="days-counter__days" style="display: none">дней</span>
                    </div>
                    <div class="filter-input filter-input_dateto">
                        <div class="filter__label filter__label_dateto">Обратно</div>
                        <?= $form->field($model, 'dateTo')->textInput([
                            'class' => 'date-input date-input_dateto js-datepicker',
                            'autocomplete' => 'off',
                            'readonly'     => 'readonly'
                        ]); ?>
                        <?= \yii\bootstrap\Html::error($model, 'dateTo'); ?>
                    </div>
                </div>
                <div class="filter__ppl-quantity ppl-quantity">
                    <div class="filter__label">Кол-во людей</div>
                    <div class="counter">
                        <?= $form->field($model, 'travellersCount', ['addon' => [
                            'prepend' => ['content'=>'<a href="#" class="change_travellersCount" data-kol="-1">
<div class="counter__minus">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14" height="14" viewBox="0 0 14 14" xml:space="preserve">
                                    <rect style="fill: #fff;" width="16" height="2" y="7"></rect>
                                </svg>
                        </div>
</a>'],
                            'append' => ['content' => '<a href="#" class="change_travellersCount" data-kol="1">
 <div class="counter__plus">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 14 14" width="14" height="14" xml:space="preserve">
                                <rect style="fill:#fff;" width="2" height="14" x="6"></rect>
                                <rect style="fill: #fff;" width="14" height="2" y="6"></rect>
                            </svg>
                        </div>
</a>']
                        ]])->textInput([
                            'class' => 'ppl-input',
                            'autocomplete' => 'off',
                            'readonly'     => 'readonly'
                        ]); ?>
                        <?= \yii\bootstrap\Html::error($model, 'dateTo'); ?>

                    </div>
                </div>
                <div class="filter__submit">
                    <button type="submit" class="black-btn black-btn_calc">Найти страховку</button>
                </div>
            </div>
        </div>
        <?php \frontend\components\ActiveForm::end() ?>