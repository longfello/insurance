<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Domain */
/* @var $form \common\components\MLActiveForm */
?>


  <div>

      <?php $form = \common\components\MLActiveForm::begin(); ?>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#domain-home" aria-controls="domain-home" role="tab"
                                                data-toggle="tab">Основная информация</a></li>
      <li role="presentation"><a href="#domain-texts" aria-controls="domain-texts" role="tab"
                                 data-toggle="tab">Тексты</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="domain-home">
        <div class="domain-form">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model,
                'city_id')->dropDownList(['' => '-- не выбран --'] + \yii\helpers\ArrayHelper::map(\common\modules\geo\models\GeoName::find()->localized()->orderBy(['name' => SORT_ASC])->asArray()->all(),
                    'id', 'name')) ?>

            <?= $form->field($model,
                'country_id')->dropDownList(['' => '-- не выбрана --'] + \yii\helpers\ArrayHelper::map(\common\modules\geo\models\GeoCountry::find()->localized()->orderBy(['name' => SORT_ASC])->asArray()->all(),
                    'id', 'name')) ?>

            <?= $form->field($model, 'default')->dropDownList([0 => 'Обычный домен', 1 => 'Основной домен']) ?>

            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'enabled')->dropDownList([0 => 'Запрещен', 1 => 'Разрешен']) ?>

            <?= $form->field($model,
                'default_language')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Languages::find()->orderBy(['name' => SORT_ASC])->localized()->asArray()->all(),
                'id', 'name')) ?>

          <div class="form-group language-multiselect">
            <label class="control-label">Языки, доступные для домена</label>
            <div class="clearfix"></div>

              <?= \dosamigos\multiselect\MultiSelect::widget([
                  'id'            => "multi-language",
                  "options"       => ['multiple' => "multiple", 'class' => 'form-control'],
                  // for the actual multiselect
                  'data'          => \yii\helpers\ArrayHelper::map(\common\models\Languages::find()->orderBy(['name' => SORT_ASC])->all(),
                      'id', 'name'),
                  'value'         => \yii\helpers\ArrayHelper::map(\common\models\Domain2Language::find()->where(['domain_id' => $model->id])->all(),
                      'language_id', 'language_id'),
                  'name'          => 'Language[]',
                  // name for the form
                  "clientOptions" =>
                      [
                          "includeSelectAllOption" => true,
                          'numberDisplayed'        => 10
                      ],
              ]); ?>
          </div>

        </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="domain-texts">
          <?php
          $models = \common\models\WidgetText::findAll(['status' => \common\models\WidgetText::STATUS_ACTIVE]);
          foreach ($models as $text) {
              ?>
            <div class="form-group field-domain-name required">
              <label class="control-label" for="domain-name"><?= $text->title ?></label>
              <a class="pull-right" target="_blank" href="/domain/update-text?id=<?= $model->id ?>&tid=<?= $text->id ?>"><span class="glyphicon glyphicon-pencil"></span></a>
            </div>
            <div class="clearfix"></div>

              <?php
          }
          ?>

      </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

      <?php \common\components\MLActiveForm::end(); ?>


  </div>


<?php

$this->registerCss("
.language-multiselect .btn-group { width:100% !important; }
.language-multiselect .multiselect-container { width:100% !important; }
.language-multiselect .multiselect-container li { width:12.5% !important; display: inline-block; }
.language-multiselect button.multiselect { width:100% !important; }
");