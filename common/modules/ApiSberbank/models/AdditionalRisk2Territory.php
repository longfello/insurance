<?php

namespace common\modules\ApiSberbank\models;

use Yii;

/**
 * This is the model class for table "api_sberbank_risk2territory".
 *
 * @property int $risk_id
 * @property int $territory_id
 *
 * @property Risk $risk
 * @property Territory $territory
 */
class AdditionalRisk2Territory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_sberbank_additional_risk2territory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['risk_id', 'territory_id'], 'required'],
            [['risk_id', 'territory_id'], 'integer'],
            [['risk_id', 'territory_id'], 'unique', 'targetAttribute' => ['risk_id', 'territory_id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdditionalRisk::className(), 'targetAttribute' => ['risk_id' => 'id']],
            [['territory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Territory::className(), 'targetAttribute' => ['territory_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'risk_id' => 'Risk ID',
            'territory_id' => 'Territory ID',
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
    public function getTerritory()
    {
        return $this->hasOne(Territory::className(), ['id' => 'territory_id']);
    }
}
