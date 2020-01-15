<?php

namespace common\components\Calculator\models\travel;

use Yii;

/**
 * This is the model class for table "filter_solution2param".
 *
 * @property integer $filter_solution_id
 * @property integer $param_id
 * @property string $value
 *
 * @property FilterSolution $filterSolution
 * @property FilterParam $param
 */
class FilterSolution2param extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'filter_solution2param';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filter_solution_id', 'param_id'], 'required'],
            [['filter_solution_id', 'param_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['filter_solution_id'], 'exist', 'skipOnError' => true, 'targetClass' => FilterSolution::className(), 'targetAttribute' => ['filter_solution_id' => 'id']],
            [['param_id'], 'exist', 'skipOnError' => true, 'targetClass' => FilterParam::className(), 'targetAttribute' => ['param_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'filter_solution_id' => 'Filter Solution ID',
            'param_id' => 'Param ID',
            'value' => 'Value',
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
     * Параметр фильтра
     * @return \yii\db\ActiveQuery
     */
    public function getParam()
    {
        return $this->hasOne(FilterParam::className(), ['id' => 'param_id']);
    }
}
