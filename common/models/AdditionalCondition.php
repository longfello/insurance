<?php

namespace common\models;

use Yii;

/**
 * Дополнительные условия.
 * This is the model class for table "additional_condition".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $slug
 */
class AdditionalCondition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'additional_condition';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['description'], 'string'],
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
            'name' => Yii::t('backend', 'Наименование'),
            'description' => Yii::t('backend', 'Описание'),
            'slug' => Yii::t('backend', 'Код'),
        ];
    }
}
