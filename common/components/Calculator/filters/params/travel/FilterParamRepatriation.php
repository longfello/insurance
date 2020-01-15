<?php
namespace common\components\Calculator\filters\params\travel;


use common\models\CostInterval;
use common\components\Calculator\models\travel\FilterParam;
use trntv\aceeditor\AceEditor;
use yii\base\Component;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;


/**
 * Фильтр репатриации
 * Class FilterParamAccident
 * @package common\components\Calculator\filters\params\travel
 */
class FilterParamRepatriation extends FilterParamPrototype
{
    /** @inheritdoc */
    public $slug = self::SLUG_REPATRIATION;
}