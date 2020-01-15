<?php

namespace common\components\Calculator\models\travel;

use common\models\GeoCountry;
use Yii;

/**
 * This is the model class for table "filter_solution2country".
 *
 * @property integer $filter_solution_id
 * @property integer $country_id
 *
 * @property FilterSolution $filterSolution
 * @property GeoCountry $country
 */
class FilterSolution2country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'filter_solution2country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filter_solution_id', 'country_id'], 'required'],
            [['filter_solution_id', 'country_id'], 'integer'],
            [['filter_solution_id'], 'exist', 'skipOnError' => true, 'targetClass' => FilterSolution::className(), 'targetAttribute' => ['filter_solution_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['country_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'filter_solution_id' => 'Filter Solution ID',
            'country_id' => 'Country ID',
        ];
    }

    /**
     * Готовое решение
     * @return \yii\db\ActiveQuery
     */
    public function getFilterSolution()
    {
        return $this->hasOne(FilterSolution::className(), ['id' => 'filter_solution_id']);
    }

    /**
     * Страна
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(GeoCountry::className(), ['id' => 'country_id']);
    }
}
