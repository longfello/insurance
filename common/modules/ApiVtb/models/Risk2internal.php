<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use Yii;

/**
 * Соответствие внутренним рискам
 * This is the model class for table "api_vtb_risk2internal".
 *
 * @property integer $risk_id
 * @property integer $internal_id
 *
 * @property Risk $risk
 * @property \common\models\Risk $internal
 */
class Risk2internal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_risk2internal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['risk_id', 'internal_id'], 'required'],
            [['risk_id', 'internal_id'], 'integer'],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'id']],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\Risk::className(), 'targetAttribute' => ['internal_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'risk_id' => 'Risk ID',
            'internal_id' => 'Internal ID',
        ];
    }

    /**
     * Риск АПИ
     * @return \yii\db\ActiveQuery
     */
    public function getRisk()
    {
        return $this->hasOne(Risk::className(), ['id' => 'risk_id']);
    }

    /**
     * Риск внутренний
     * @return \yii\db\ActiveQuery
     */
    public function getInternal()
    {
        return $this->hasOne(\common\models\Risk::className(), ['id' => 'internal_id']);
    }
}
