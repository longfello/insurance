<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\models;

use Yii;

/**
 * Категория риска
 * This is the model class for table "risk_category".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $sort_order
 */
class RiskCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'risk_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['id', 'sort_order'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'name' => Yii::t('backend', 'Название'),
            'description' => Yii::t('backend', 'Описание'),
            'sort_order' => Yii::t('backend', 'Порядок сортировки'),
        ];
    }
}
