<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;

/**
 * Соотношение рисков АПИ рискам внутренним
 * This is the model class for table "api_tinkoff_risk2internal".
 *
 * @property integer $risk_id
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
        return 'api_tinkoff_risk2internal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['risk_id', 'internal_id'], 'required'],
            [['risk_id', 'internal_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'risk_id' => 'Id риска',
            'internal_id' => 'Id из внутреннего справочника',
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
        return $this->hasOne(Risk::className(), ['id' => 'risk_id']);
    }
}
