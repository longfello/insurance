<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;

/**
 * Ассистенты
 * This is the model class for table "assistance".
 *
 * @property integer $assistanteID
 * @property string $assistanteUID
 * @property string $assistanceCode
 * @property string $assistanceName
 * @property string $assistancePhones
 */
class Assistance extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_assistance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['assistanteID', 'assistanteUID', 'assistanceCode', 'assistanceName', 'assistancePhones'], 'required'],
            [['assistanteID'], 'integer'],
            [['assistanteUID'], 'string', 'max' => 36],
            [['assistanceCode'], 'string', 'max' => 64],
            [['assistancePhones'], 'string', 'max' => 1024],
            [['assistanceName'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'assistanteID' => Yii::t('backend', 'ID ассистента'),
            'assistanteUID' => Yii::t('backend', 'GUID ассистента'),
            'assistanceCode' => Yii::t('backend', 'Код ассистента'),
            'assistanceName' => Yii::t('backend', 'Название ассистента'),
            'assistancePhones' => Yii::t('backend', 'Телефоны'),
        ];
    }
}
