<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;

/**
 * Соответствие риска программам страхования
 * This is the model class for table "api_alpha_risk2program".
 *
 * @property integer $program_id
 * @property integer $risk_id
 *
 * @property InsuranceProgramm $program
 * @property Risk $risk
 * @property string $parent_id
 */
class Risk2program extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_risk2program';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'risk_id'], 'required'],
            [['program_id', 'risk_id'], 'integer'],
            [['parent_id'], 'string', 'max' => 255],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => InsuranceProgramm::className(), 'targetAttribute' => ['program_id' => 'insuranceProgrammID']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'riskID']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'program_id' => 'Program ID',
            'risk_id' => 'Risk ID',
            'parent_id' => 'Соответствует риску во внутреннем справочнике',
        ];
    }

    /**
     * Программы страхвания
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(InsuranceProgramm::className(), ['insuranceProgrammID' => 'program_id']);
    }

    /**
     * Риски
     * @return \yii\db\ActiveQuery
     */
    public function getRisk()
    {
        return $this->hasOne(Risk::className(), ['riskID' => 'risk_id']);
    }

    /**
     * Внутренние риски
     * @return \common\models\Risk[]
     */
    public function getInternalRisks(){
        return \common\models\Risk::findAll(['id' => explode(',', $this->parent_id)]);
    }
}
