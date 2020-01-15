<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiErv\models;

use common\models\Risk;
use Yii;

/**
 * Соответствие рисков програме
 * This is the model class for table "api_erv_program2risk".
 *
 * @property integer $program_id
 * @property integer $risk_id
 * @property string $summa
 * @property integer $is_optional
 *
 * @property Program $program
 * @property Risk $risk
 */
class Program2Risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_erv_program2risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'risk_id'], 'required'],
            [['program_id', 'risk_id', 'is_optional'], 'integer'],
            [['summa'], 'number'],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'program_id' => Yii::t('backend', 'Program ID'),
            'risk_id' => Yii::t('backend', 'Risk ID'),
            'summa' => Yii::t('backend', 'Summa'),
            'is_optional' => 'Дополнительный риск'
        ];
    }

    /**
     * Програма
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    /**
     * Риск
     * @return \yii\db\ActiveQuery
     */
    public function getRisk()
    {
        return $this->hasOne(Risk::className(), ['id' => 'risk_id']);
    }
}
