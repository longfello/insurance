<?php

namespace common\components\Calculator\models\travel;

use Yii;
use common\models\Api;

/**
 * This is the model class for table "filter_solution2api".
 *
 * @property integer $id
 * @property integer $filter_solution_id
 * @property integer $api_id
 *
 * @property FilterSolution $solution
 * @property Api $api
 */
class FilterSolution2api extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'filter_solution2api';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filter_solution_id', 'api_id'], 'required'],
            [['filter_solution_id', 'api_id'], 'integer'],
            [['filter_solution_id'], 'exist', 'skipOnError' => true, 'targetClass' => FilterSolution::className(), 'targetAttribute' => ['filter_solution_id' => 'id']],
            [['api_id'], 'exist', 'skipOnError' => true, 'targetClass' => Api::className(), 'targetAttribute' => ['api_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filter_solution_id' => 'Solution ID',
            'api_id' => 'Api ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSolution()
    {
        return $this->hasOne(FilterSolution::className(), ['id' => 'filter_solution_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApi()
    {
        return $this->hasOne(Api::className(), ['id' => 'api_id']);
    }
}
