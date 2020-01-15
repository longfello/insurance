<?php

namespace common\modules\ApiRgs\models;

use Yii;
use common\models\Risk;

/**
 * Соответствие рисков программам страхования
 * This is the model class for table "api_rgs_program_risk".
 *
 * @property int $program_id
 * @property int $risk_id
 */
class ProgramRisk extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_rgs_program_risk';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['program_id', 'risk_id'], 'required'],
            [['program_id', 'risk_id'], 'integer'],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'program_id' => Yii::t('backend', 'Внутренний ID программы'),
            'risk_id' => Yii::t('backend', 'Внутренний ID риска')
        ];
    }

    /**
     * Программа
     * 
     * @return Program
     */
    public function getProgramModel() {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
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
