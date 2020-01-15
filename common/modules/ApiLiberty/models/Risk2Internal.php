<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Соответствие риска внутреннему
 * This is the model class for table "api_liberty_risk2internal".
 *
 * @property integer $riskId
 * @property integer $internal_id
 *
 * @property \common\models\Risk $internalRiskModel
 * @property Risk $apiModel
 */
class Risk2Internal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_risk2internal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['riskId', 'internal_id'], 'required'],
            [['riskId', 'internal_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'riskId' => 'Ид риска',
            'internal_id' => 'Внутренний id',
            'amount' => 'Страховая сумма',
        ];
    }

    /**
     * Внутренний риск
     * @return \yii\db\ActiveQuery
     */
    public function getInternalRiskModel()
    {
        return $this->hasOne(\common\models\Risk::className(), ['id' => 'internal_id']);
    }

    /**
     * Риск АПИ
     * @return \yii\db\ActiveQuery
     */
    public function getApiModel(){
        return $this->hasOne(Risk::className(), ['riskId' => 'riskId']);
    }
}
