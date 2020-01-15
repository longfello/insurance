<?php

namespace common\modules\ApiSberbank\models;

use Yii;

/**
 * Соответствие территорий программам
 * This is the model class for table "api_sberbank_territory2program".
 *
 * @property int $territory_id
 * @property int $program_id
 *
 * @property Program $program
 * @property Territory $territory
 */
class Territory2Program extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_sberbank_territory2program';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['territory_id', 'program_id'], 'required'],
            [['territory_id', 'program_id'], 'integer'],
            [['territory_id', 'program_id'], 'unique', 'targetAttribute' => ['territory_id', 'program_id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['territory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Territory::className(), 'targetAttribute' => ['territory_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'territory_id' => 'Ид территории',
            'program_id' => 'Ид программы',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerritory()
    {
        return $this->hasOne(Territory::className(), ['id' => 'territory_id']);
    }
}
