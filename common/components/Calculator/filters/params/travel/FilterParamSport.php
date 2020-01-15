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
 * Фильтр спорта
 * Class FilterParamAccident
 * @package common\components\Calculator\filters\params\travel
 */
class FilterParamSport extends FilterParamPrototype {
    /** @inheritdoc */
	public $slug = self::SLUG_SPORT;
}