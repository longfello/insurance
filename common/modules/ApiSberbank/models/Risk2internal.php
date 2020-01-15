<?php

namespace common\modules\ApiSberbank\models;

use Yii;

/**
 * This is the model class for table "api_sberbank_risk2internal".
 *
 * @property int $risk_id
 * @property int $internal_id
 *
 * @property ApiSberbankRisk $risk
 * @property Risk $internal
 */
class Risk2internal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_sberbank_risk2internal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['risk_id', 'internal_id'], 'required'],
            [['risk_id', 'internal_id'], 'integer'],
            [['risk_id', 'internal_id'], 'unique', 'targetAttribute' => ['risk_id', 'internal_id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => ApiSberbankRisk::className(), 'targetAttribute' => ['risk_id' => 'id']],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['internal_id' => 'id']],
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
     * @return \yii\db\ActiveQuery
     */
    public function getRisk()
    {
        return $this->hasOne(ApiSberbankRisk::className(), ['id' => 'risk_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInternal()
    {
        return $this->hasOne(Risk::className(), ['id' => 'internal_id']);
    }
}
