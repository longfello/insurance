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
 * Фильтр несчастного случая
 * Class FilterParamAccident
 * @package common\components\Calculator\filters\params\travel
 */
class FilterParamAccident extends FilterParamPrototype {
    /** @inheritdoc */
    public $slug = self::SLUG_ACCIDENT;
}