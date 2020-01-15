<?php

namespace common\modules\ApiSberbank\models;

use common\models\Risk;
use Yii;

/**
 * Соответствие рисков програме
 * This is the model class for table "api_sberbank_program2risk".
 *
 * @property int $program_id
 * @property int $risk_id
 * @property string $summa
 * @property int $is_optional Опциональный риск
 * @property string $name
 *
 * @property Risk $risk
 * @property Program $program
 */
class Program2Risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_sberbank_program2risk';
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
            [['name'], 'string', 'max' => 255],
            [['program_id', 'risk_id'], 'unique', 'targetAttribute' => ['program_id', 'risk_id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
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
            'summa' => 'Summa',
            'is_optional' => 'Is Optional',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRisk()
    {
        return $this->hasOne(Risk::className(), ['id' => 'risk_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }
}
