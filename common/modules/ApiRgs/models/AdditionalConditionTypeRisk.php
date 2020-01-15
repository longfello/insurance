<?php

namespace common\modules\ApiRgs\models;

use Yii;
use common\models\Risk;
use common\modules\ApiRgs\models\AdditionalConditionType;

/**
 * Соответствие доп. условий рискам во внутреннем справочнике
 * This is the model class for table "api_rgs_additional_condition_type_risk".
 *
 * @property int $additional_condition_type_id
 * @property int $risk_id
 */
class AdditionalConditionTypeRisk extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_rgs_additional_condition_type_risk';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['additional_condition_type_id', 'risk_id'], 'required'],
            [['additional_condition_type_id', 'risk_id'], 'integer'],
            [['additional_condition_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdditionalConditionType::className(), 'targetAttribute' => ['additional_condition_type_id' => 'id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'additional_condition_type_id' => Yii::t('backend', 'Внутренний ID вида доп. условия'),
            'risk_id' => Yii::t('backend', 'Внутренний ID риска')
        ];
    }

    /**
     * Вид доп. условия
     * 
     * @return AdditionalConditionType
     */
    public function getAdditionalConditionTypeModel() {
        return $this->hasOne(AdditionalConditionType::className(), ['id' => 'additional_condition_type_id']);
    }

    /**
     * Риск
     * 
     * @return Risk
     */
    public function getRiskModel() {
        return $this->hasOne(Risk::className(), ['id' => 'risk_id']);
    }

}
