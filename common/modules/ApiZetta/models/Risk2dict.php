<?php

namespace common\modules\ApiZetta\models;

use Yii;
use common\models\Risk as Dict;

/**
 * Соответствие риска во внутреннем справочнике
 * This is the model class for table "api_zetta_risk2dict".
 *
 * @property int $internal_id
 * @property int $risk_id
 */
class Risk2dict extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_zetta_risk2dict';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['internal_id', 'risk_id'], 'required'],
            [['internal_id', 'risk_id'], 'integer'],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dict::className(), 'targetAttribute' => ['internal_id' => 'id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'internal_id' => Yii::t('backend', 'ID во внутреннем справочнике'),
            'risk_id' => Yii::t('backend', 'ID риска по АПИ')
        ];
    }

    /**
     * Риск во внутреннем справочнике
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRiskModel() {
        return $this->hasOne(Dict::className(), ['id' => 'internal_id']);
    }

    /**
     * Риск АПИ
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getApiModel() {
        return $this->hasOne(Risk::className(), ['id' => 'risk_id']);
    }

}
