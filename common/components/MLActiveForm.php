<?php

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.08.15
 * Time: 13:25
 */
namespace common\components;
use trntv\aceeditor\AceEditor;
use \Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveField;

/**
 * Class MLActiveForm Мультиязычные контролы
 * @package common\components
 */
class MLActiveForm extends \yii\bootstrap\ActiveForm  {
    /**
     * Редактор IMPERAVI
     */
    const VISUAL_MODE_IMPERAVI = '1';
    /**
     * Редактор ACE
     */
    const VISUAL_MODE_ACE = '2';


    /**
     * @var string Язык
     */
    public $language = '';
    /**
     * @var array Доступные языки
     */
    public $languages = [];
    /**
     * @var string Язык по-умолчанию
     */
    public $defaultLanguage = '';

    /**
     * MLActiveForm constructor.
     *
     * @param array $config
     */
    public function __construct($config = []){
        $this->languages       = Yii::$app->params['availableLocales']; //\common\models\I18nLanguages::getAvailableLanguages();
        return parent::__construct($config);
      }

    /**
     * Виджет текстовое поле
     * @param $model
     * @param $field
     * @param string $inputPrefix
     *
     * @return string
     */
    public function textFieldGroup($model, $field, $inputPrefix = '') {
    /** @var $model ActiveRecord */
    $prefix   = $this->getShortClassName($model->className()).'-'.$inputPrefix;
    $content  = "<div class='{$field}-field-wrapper'>";
    $content .= "<ul class='nav nav-tabs' role='tablist'>";
    $first = true; $errors='';

    foreach($this->languages as $iso => $name) {
      $iso = substr($iso, 0, 2);
      $active = $first?"active":"";
      $content .= "<li role='presentation' class='$active'><a href='#{$prefix}-{$field}-{$iso}' aria-controls='{$field}-{$iso}' role='tab' data-toggle='tab'>{$name}</a></li>";
      $first = false;
    }
    $content .= ("</ul><div class='tab-content'>");
    $first = true;
    foreach($this->languages as $iso => $name) {
      $iso      = substr($iso, 0, 2);
      $active   = $first?"active":"";
        $suffix   = ($iso == substr(Yii::$app->sourceLanguage,0,2))?"":"_".$iso;
      $content .= "<div role='tabpanel' class='tab-pane $active' id='{$prefix}-{$field}-{$iso}'>";
      $options  = [
        'class' => 'form-control',
        'autocomplete' => 'off',
        'name' => $this->getShortClassName($model->className())."[{$field}{$suffix}]",
      ];
      if ($inputPrefix) {
        $options['name'] = $this->getShortClassName($model->className())."[{$inputPrefix}][{$field}{$suffix}]";
      }
      $content .= $this->field($model, $field.$suffix)->textInput($options);
      $content .= "</div>";
      $errors  .= Html::error($model, $field.$suffix);
      $first    = false;
    }
    $content .= "</div>";
    $content .= $errors;
    $content .= "</div>";
    return $content;
  }

    /**
     * Виджет редактирования JSON
     * @param $model
     * @param $field
     *
     * @return string
     */
    public function textAreaJsonGroup($model, $field) {
    $content =  "<div class='{$field}-field-wrapper'>";

    $content .= "<ul class='nav nav-tabs' role='tablist'>";
    $first = true; $errors='';
    foreach($this->languages as $iso => $name) {
      $iso = substr($iso, 0, 2);
      $active = $first?"active":"";
      $content .= "<li role='presentation' class='$active'><a href='#{$field}-{$iso}' aria-controls='{$field}-{$iso}' role='tab' data-toggle='tab'>{$name}</a></li>";
      $first = false;
    }
    $content .= "</ul>";
    $first = true;

    $content .= "<div class='tab-content'>";
    foreach($this->languages as $iso => $name) {
      $iso = substr($iso, 0, 2);
      $active = $first?"active":"";
      $suffix   = ($iso == substr(Yii::$app->sourceLanguage,0,2))?"":"_".$iso;
//      $suffix   = '_'.$iso;
      $content .= "<div role='tabpanel' class='tab-pane $active' id='{$field}-{$iso}'>";

      $content .= $this->field($model, $field.$suffix)->widget(
          AceEditor::className(),
          [
              'mode' => 'json'
          ]
      );
      $content .= '<p class="help-block"></p>';
      $content .= "</div>";
      $errors  .= Html::error($model, $field.$suffix);
      $first = false;
    }
    $content .= "</div>";

    $content .= $errors;
    $content .= "</div>";
    return $content;
  }

    /**
     * Виджет визуального редактора
     * @param $model
     * @param $field
     * @param array $options
     * @param string $mode
     *
     * @return string
     */
     public function textAreaVisualGroup($model, $field, $options = [], $mode = self::VISUAL_MODE_IMPERAVI) {
    $content =  "<div class='{$field}-field-wrapper'>";

    $content .= "<ul class='nav nav-tabs' role='tablist'>";
    $first = true; $errors='';
    foreach($this->languages as $iso => $name) {
      $iso = substr($iso, 0, 2);
      $active = $first?"active":"";
      $content .= "<li role='presentation' class='$active'><a href='#{$field}-{$iso}' aria-controls='{$field}-{$iso}' role='tab' data-toggle='tab'>{$name}</a></li>";
      $first = false;
    }
    $content .= "</ul>";
    $first = true;

    $content .= "<div class='tab-content'>";
    foreach($this->languages as $iso => $name) {
      $iso = substr($iso, 0, 2);
      $active = $first?"active":"";
      $suffix   = ($iso == substr(Yii::$app->sourceLanguage,0,2))?"":"_".$iso;
//      $suffix   = '_'.$iso;
      $content .= "<div role='tabpanel' class='tab-pane $active' id='{$field}-{$iso}'>";

      switch ($mode){
          case self::VISUAL_MODE_IMPERAVI:
              $options = array_merge([
                  'minHeight' => 400,
                  'maxHeight' => 400,
                  'buttonSource' => true,
                  'convertDivs' => false,
                  'removeEmptyTags' => false,
                  'imageUpload' => Yii::$app->urlManager->createUrl(['/file-storage/upload-imperavi'])
              ], $options);
              $content .= $this->field($model, $field.$suffix)->widget(
                  \yii\imperavi\Widget::className(),
                  [
                      'plugins' => ['fullscreen', 'fontcolor', 'video'],
                      'options' => $options
                  ]
              );
              break;
          case self::VISUAL_MODE_ACE:
              $options = array_merge([
                  'minHeight' => 400,
                  'maxHeight' => 400,
              ], $options);
              $content .= $this->field($model, $field.$suffix)->widget(
                  AceEditor::className(),
                  [
                      'mode' => 'html',
                      'options' => $options
                  ]
              );
              break;
      }
      $content .= '<p class="help-block"></p>';
      $content .= "</div>";
      $errors  .= Html::error($model, $field.$suffix);
      $first = false;
    }
    $content .= "</div>";

    $content .= $errors;
    $content .= "</div>";
    return $content;
  }

    /**
     * Виджет textarea
     * @param $model
     * @param $field
     *
     * @return string
     */
    public function textAreaGroup($model, $field) {
    $content = "<div class='{$field}-field-wrapper'>";
    $content .= "<ul class='nav nav-tabs' role='tablist'>";
    $first = true; $errors = '';
    foreach($this->languages as $iso => $name) {
      $iso = substr($iso, 0, 2);
      $active = $first?"active":"";
      $content .= ("<li role='presentation' class='$active'><a href='#{$field}-{$iso}' aria-controls='{$field}-{$iso}' role='tab' data-toggle='tab'>{$name}</a></li>");
      $first = false;
    }
    $content .= ("</ul><div class='tab-content'>");
    $first = true;
    foreach($this->languages as $iso => $name) {
      $iso = substr($iso, 0, 2);
      $active = $first?"active":"";
      $suffix   = ($iso == substr(Yii::$app->sourceLanguage,0,2))?"":"_".$iso;
//      $suffix   = '_'.$iso;
      $content .= ("<div role='tabpanel' class='tab-pane $active' id='{$field}-{$iso}'>");
      $content .= $this->field($model, $field.$suffix)->textarea(['class' => 'form-control']);
      $content .= ("</div>");
      $errors  .= Html::error($model, $field.$suffix);
      $first = false;
    }
    $content .= "</div>";
    $content .= ($errors);
    $content .= "</div>";

    return $content;
  }

  /**
   * Виджет датапикера
   * @param $model
   * @param $field
   * @param array $options
   * @return ActiveField
   */
    public function datePicker($model, $field, $options = []){
    /** @var $model ActiveRecord */
    $id = uniqid($this->getId());

    $value = intval(Yii::$app->formatter->asTimestamp($model->getAttribute($field)));

    $options['wrapper'] = isset($options['wrapper'])?$options['wrapper']:true;
    $options['class'] = isset($options['class'])?$options['class']:'form-control';
    $options['id'] = $id;

    if ($value > 0) {
      $options['value'] = Yii::$app->formatter->asDate($value, 'php:d/m/Y');
    } else {
      $options['value'] = '';
    }

    $hiddenOptions = $options;
    $hiddenOptions['id'] = $options['id'].'-c';
    $hiddenOptions['value'] = date('Y-m-d', $value);

    if ($options['wrapper']) {
      $content  = $this->field($model, $field)->textInput($options);
    } else {
      $content  = $this->field($model, $field, [
          'template'=>'{input}',
          'inputTemplate' => '{input}',
          'options' => [
              'tag'=>'span'
          ],
      ])->textInput($options);
    }
    $content .= Html::activeHiddenInput($model, $field, $hiddenOptions);

    $paths = \Yii::$app->assetManager->publish('@bower/admin-lte/plugins');
    $url   = $paths[1];
    Yii::$app->view->registerJsFile($url.'/daterangepicker/moment.js');
    Yii::$app->view->registerJsFile($url.'/daterangepicker/daterangepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    Yii::$app->view->registerCssFile($url.'/daterangepicker/daterangepicker-bs3.css');
    Yii::$app->view->registerJs("
(function(){
  var el = $('#{$id}-c');
  var copy = $('#{$id}').removeAttr('name');

  $(copy).daterangepicker({
    singleDatePicker: true,
    format: 'DD/MM/YYYY'
  }, function(start, end){
      $(copy).val(start.format('DD/MM/YYYY'));
      $(el).val(start.format('YYYY-MM-DD'));
  });

})();
");
    return $content;
  }

  /**
   * Виджет интервального датапикера
   * @param $model
   * @param $field
   * @param array $options
   * @return ActiveField
   */
    public function dateRangePicker($model, $field, $options = []){
    /** @var $model ActiveRecord */
    $id = uniqid($this->getId());

    $options['wrapper'] = isset($options['wrapper'])?$options['wrapper']:true;
    $options['class'] = isset($options['class'])?$options['class']:'form-control';
    $options['id'] = $id;

    $hiddenOptions = $options;
    $hiddenOptions['id'] = $options['id'].'-c';

    $fieldValue = $model->getAttribute($field);
    if ($fieldValue) {
      $start = $end = '';
      list($start, $end) = explode('|', $fieldValue);
      if ($start && $end) {
        $options['value'] = Yii::$app->formatter->asDate($start, 'php:d/m/Y').'-'.Yii::$app->formatter->asDate($end, 'php:d/m/Y');
        $hiddenOptions['value'] = Yii::$app->formatter->asDate($start, 'php:Y/m/d').'|'.Yii::$app->formatter->asDate($end, 'php:Y/m/d');
      }
    }

    if ($options['wrapper']) {
      $content  = $this->field($model, $field)->textInput($options);
    } else {
      $content  = $this->field($model, $field, [
          'template'=>'{input}',
          'inputTemplate' => '{input}',
          'options' => [
              'tag'=>'span'
          ],
      ])->textInput($options);
    }
    $content .= Html::activeHiddenInput($model, $field, $hiddenOptions);


    $paths = \Yii::$app->assetManager->publish('@bower/admin-lte/plugins');
    $url   = $paths[1];
    Yii::$app->view->registerJsFile($url.'/daterangepicker/moment.js');
    Yii::$app->view->registerJsFile($url.'/daterangepicker/daterangepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    Yii::$app->view->registerCssFile($url.'/daterangepicker/daterangepicker-bs3.css');
    Yii::$app->view->registerJs("
(function(){
  var el = $('#{$id}-c').hide();
  var copy = $('#{$id}').removeAttr('name');

  $(copy).daterangepicker({
    format: 'DD/MM/YYYY',
    locale: {
        format: 'DD/MM/YYYY',
        separator: ' - ',
        applyLabel: '".Yii::t('frontend', 'Применить')."',
        cancelLabel: '".Yii::t('frontend', 'Отмена')."',
        fromLabel: '".Yii::t('frontend', 'С')."',
        toLabel: '".Yii::t('frontend', 'По')."',
        customRangeLabel: '".Yii::t('frontend', 'Задать диапазон')."',
        daysOfWeek: [
            '".Yii::t('frontend', 'Вс')."',
            '".Yii::t('frontend', 'Пн')."',
            '".Yii::t('frontend', 'Вт')."',
            '".Yii::t('frontend', 'Ср')."',
            '".Yii::t('frontend', 'Чт')."',
            '".Yii::t('frontend', 'Пт')."',
            '".Yii::t('frontend', 'Сб')."',
        ],
        monthNames: [
            '".Yii::t('frontend', 'Январь')."',
            '".Yii::t('frontend', 'Февраль')."',
            '".Yii::t('frontend', 'Март')."',
            '".Yii::t('frontend', 'Апрель')."',
            '".Yii::t('frontend', 'Май')."',
            '".Yii::t('frontend', 'Июнь')."',
            '".Yii::t('frontend', 'Июль')."',
            '".Yii::t('frontend', 'Август')."',
            '".Yii::t('frontend', 'Сентябрь')."',
            '".Yii::t('frontend', 'Октябрь')."',
            '".Yii::t('frontend', 'Ноябрь')."',
            '".Yii::t('frontend', 'Декабрь')."',
        ],
        firstDay: 1
    },
    ranges: {
       '".Yii::t('frontend', 'Сегодня - завтра')."': [moment(), moment().add(1, 'days')],
       '".Yii::t('frontend', 'На этой неделе')."': [moment().startOf('week').add(1, 'days'), moment().endOf('week').add(1, 'days')],
       '".Yii::t('frontend', 'В этом месяце')."': [moment(), moment().endOf('month')],
    }
  }, function(start, end){
      $(copy).val(start.format('DD/MM/YYYY')+'-'+end.format('DD/MM/YYYY'));
      $(el).val(start.format('YYYY-MM-DD')+'|'+end.format('YYYY-MM-DD'));
  });

})();
");
    return $content;
  }

    /**
     * Виджет дататаймпикера
     * @param $model
     * @param $field
     * @param array $options
     *
     * @return $this|string
     */
     public function dateTimePicker($model, $field, $options = []){
    /** @var $model ActiveRecord */
    $id = uniqid($this->getId());

    // $value = intval(Yii::$app->formatter->asTimestamp($model->getAttribute($field)));
    $value = intval(@strtotime($model->getAttribute($field)));
    $options = array_merge($options, [
      'class' => 'form-control',
      'id' => $id,
    ]);
    if ($value > 0) {
      $options['value'] = date('d/m/Y H:i', $value);
    } else {
      $options['value'] = '';
    }

    $hiddenOptions = $options;
    $hiddenOptions['id'] = $options['id'].'-c';
    $hiddenOptions['value'] = date('Y-m-d H:i:00', $value);

    $content  = $this->field($model, $field)->textInput($options);
    $content .= Html::activeHiddenInput($model, $field, $hiddenOptions);

    $paths = \Yii::$app->assetManager->publish('@bower/admin-lte/plugins');
    $url   = $paths[1];
    Yii::$app->view->registerJsFile($url.'/daterangepicker/moment.js');
    Yii::$app->view->registerJsFile($url.'/daterangepicker/daterangepicker.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    Yii::$app->view->registerCssFile($url.'/daterangepicker/daterangepicker-bs3.css');
    Yii::$app->view->registerJs("
(function(){
  var el = $('#{$id}-c');
  var copy = $('#{$id}').removeAttr('name');

  $(copy).daterangepicker({
    singleDatePicker: true,
    timePicker: true,
    timePickerIncrement: 15,
    timePicker12Hour: false,
    format: 'DD/MM/YYYY HH:mm'
  }, function(start, end){
      $(copy).val(start.format('DD/MM/YYYY HH:mm'));
      $(el).val(start.format('YYYY-MM-DD HH:mm:00'));
  });

})();
");
    return $content;
  }
/*
  function coordinates($model){
    $unique = uniqid();
    $additional_class_prefix = "coordinates-".$unique;
    $class  = "form-control ".$additional_class_prefix;

    return "
<div class='coordinates-widget'>
  <div class='row'>
    <div class='col-xs-5'>
      ".$this->field($model, 'latitude')->textInput(['class'=> $class.'-latitude'])."
    </div>
    <div class='col-xs-5'>
      ".$this->field($model, 'longitude')->textInput(['class'=> $class.'-longitude'])."
    </div>
    <div class='col-xs-2'>
      <label>&nbsp;</label>
      <button type='button' class='btn btn-primary form-control' onclick='$(this).parents(\".coordinates-widget\").find(\".map-row\").toggleClass(\"hidden\"); google.maps.event.trigger($(\".map-row div div\")[0], \"resize\");'>".Yii::t('app', 'Скрыть/показать карту')."</button>
    </div>
  </div>
  <div class='row map-row hidden'>
    <div class='col-xs-12'>
    ".
      \pigolab\locationpicker\LocationPickerWidget::widget([
  //        'key' => Yii::$app->params['google_api_key'], // optional , Your can also put your google map api key
          'options' => [
              'style' => 'width: 100%; height: 300px', // map canvas width and height
          ] ,
          'clientOptions' => [
              'location' => [
                  'latitude'  => $model->latitude,
                  'longitude' => $model->longitude,
              ],
              'radius'    => 10,
              'inputBinding' => [
                  'latitudeInput'     => new JsExpression("$('.{$additional_class_prefix}-latitude')"),
                  'longitudeInput'    => new JsExpression("$('.{$additional_class_prefix}-longitude')"),
  //                'radiusInput'       => new JsExpression("$('#us2-radius')"),
  //                'locationNameInput' => new JsExpression("$('#us2-address')")
              ]
          ],
      ])
      ."
    </div>
  </div>
</div>
";
  }
*/

  /**
   * Возвращает имя класса без нэймспейса
   * @param string $className
   * @return string
   */
  public function getShortClassName($className)
  {
    return substr($className, strrpos($className, '\\') + 1);
  }


}


