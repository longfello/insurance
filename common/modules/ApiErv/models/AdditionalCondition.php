<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiErv\models;

use Yii;

/**
 * Дополнительные параметры
 * This is the model class for table "api_erv_additional_conditions".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $description
 * @property string $value
 * @property string $type
 * @property string $slug
 *
 * @property \common\models\AdditionalCondition $parent
 */
class AdditionalCondition extends \yii\db\ActiveRecord
{
    /**
     * возраст выше 65
     */
    const CASE_OVER_65       = 'OVER65';

    /**
     * возраст выше 80
     */
    const CASE_OVER_80       = 'OVER80';
    /**
     * рискованый спорт
     */
    const CASE_RISKFUL_SPORT = 'RISKFULSPORT';
    /**
     * отмена поездки
     */
    const CASE_CANCELLATION  = 'CANCELLATION';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_erv_additional_conditions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'name', 'description', 'value'], 'required'],
            [['parent_id'], 'integer'],
            [['description', 'type'], 'string'],
            [['value'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'parent_id' => Yii::t('backend', 'Эквивалент во внутреннем справочнике'),
            'name' => Yii::t('backend', 'Наименование'),
            'description' => Yii::t('backend', 'Описание'),
            'value' => Yii::t('backend', 'Величина'),
            'type' => Yii::t('backend', 'Тип'),
            'slug' => Yii::t('backend', 'Код'),
        ];
    }

	/**
     * Соответствие внутреннему справочнику рисков
	 * @return \yii\db\ActiveQuery
	 */
	public function getParent()
	{
		return $this->hasOne(\common\models\AdditionalCondition::className(), ['id' => 'parent_id']);
	}

}
