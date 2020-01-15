<?php

// namespace frontend\models;
namespace common\components\Calculator\forms;

use common\models\GeoCountry;
use common\models\Risk;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\prototype;
use frontend\models\PersonInfo;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

/**
 * Class MortgageForm Форма Ипотечное страхование
 * @package common\components\Calculator\forms
 */
class MortgageForm extends prototype
{
	/** @var string Слюг (псевдоним) типа страхования */
	public $slug = self::SLUG_MORTGAGE;
}
