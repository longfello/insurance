<?php

namespace common\modules\ApiZetta\models;

use Yii;

/**
 * Соответствие рисков программам страхования
 * This is the model class for table "api_zetta_program_risk_sum".
 *
 * @property int $program_id
 * @property int $risk_id
 * @property int $sum_id
 * @property int $sum
 */
class ProgramRiskSum extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_zetta_program_risk_sum';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey() {
        return ['program_id', 'risk_id', 'sum_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['program_id', 'risk_id', 'sum_id', 'sum'], 'required'],
            [['program_id', 'risk_id', 'sum_id', 'sum'], 'integer'],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'id']],
            [['sum_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sum::className(), 'targetAttribute' => ['sum_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'program_id' => Yii::t('backend', 'Внутренний ID программы'),
            'risk_id' => Yii::t('backend', 'Внутренний ID риска'),
            'sum_id' => Yii::t('backend', 'Внутренний ID суммы страхования'),
            'sum' => Yii::t('backend', 'Сумма покрытия')
        ];
    }

    /**
     * Программа
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getProgramModel() {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    /**
     * Риск
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getRiskModel() {
        return $this->hasOne(Risk::className(), ['id' => 'risk_id']);
    }

    /**
     * Сумма страхования
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getSumModel() {
        return $this->hasOne(Sum::className(), ['id' => 'sum_id']);
    }

}
