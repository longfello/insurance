<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;

/**
 * Дополнительные условия - справочние АПИ
 * This is the model class for table "additional_condition".
 *
 * @property integer $additionalConditionID
 * @property string $additionalCondition
 * @property string $additionalConditionUID
 * @property double $additionalConditionValue
 * @property integer $parent_id
 */
class AdditionalCondition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_additional_condition';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['additionalConditionID', 'additionalCondition', 'additionalConditionUID'], 'required'],
            [['additionalConditionID'], 'integer'],
            [['additionalConditionValue'], 'number'],
            [['additionalCondition'], 'string', 'max' => 255],
            [['additionalConditionUID'], 'string', 'max' => 36],
            [['parent_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'additionalConditionID' => Yii::t('backend', 'ID'),
            'additionalCondition' => Yii::t('backend', 'Название'),
            'additionalConditionUID' => Yii::t('backend', 'GUID'),
            'additionalConditionValue' => Yii::t('backend', 'Коэффициент увеличения тарифа'),
            'parent_id' => Yii::t('backend', 'Эквивалент во внутреннем справочнике'),
        ];
    }
}
