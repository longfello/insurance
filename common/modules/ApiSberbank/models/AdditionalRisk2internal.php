<?php

namespace common\modules\ApiSberbank\models;

use Yii;

/**
 * This is the model class for table "api_sberbank_risk2internal".
 *
 * @property int $risk_id
 * @property int $internal_id
 *
 * @property AdditionalRisk $risk
 * @property \common\models\Risk $internal
 */
class AdditionalRisk2internal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_sberbank_additional_risk2internal';
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
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdditionalRisk::className(), 'targetAttribute' => ['risk_id' => 'id']],
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
     * @return \yii\db\ActiveQuery
     */
    public function getRisk()
    {
        return $this->hasOne(AdditionalRisk::className(), ['id' => 'risk_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInternal()
    {
        return $this->hasOne(\common\models\Risk::className(), ['id' => 'internal_id']);
    }
}
