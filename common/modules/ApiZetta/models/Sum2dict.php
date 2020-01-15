<?php

namespace common\modules\ApiZetta\models;

use Yii;
use common\models\CostInterval as Dict;

/**
 * Соответствие сумм страхования во внутреннем справочнике
 * This is the model class for table "api_zetta_sum2dict".
 *
 * @property int $internal_id
 * @property int $sum_id
 */
class Sum2dict extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_zetta_sum2dict';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['internal_id', 'sum_id'], 'required'],
            [['internal_id', 'sum_id'], 'integer'],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dict::className(), 'targetAttribute' => ['internal_id' => 'id']],
            [['sum_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sum::className(), 'targetAttribute' => ['sum_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'internal_id' => Yii::t('backend', 'ID во внутреннем справочнике'),
            'sum_id' => Yii::t('backend', 'ID суммы по АПИ')
        ];
    }

    /**
     * Сумма во внутреннем справочнике
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getCostIntervalModel() {
        return $this->hasOne(Dict::className(), ['id' => 'internal_id']);
    }

    /**
     * Сумма АПИ
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getApiModel() {
        return $this->hasOne(Sum::className(), ['id' => 'sum_id']);
    }

}
