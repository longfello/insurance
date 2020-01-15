<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\models;

use Yii;

/**
 * Риск
 * This is the model class for table "risk".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $description
 * @property string $params
 * @property integer $sort_order
 * @property string|integer $variant
 *
 * @property RiskCategory $category
 */
class Risk extends \yii\db\ActiveRecord
{
    /**
     * @var mixed Вариант
     */
    public $variant;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['category_id', 'sort_order'], 'integer'],
            [['description'], 'string'],
            [['name', 'params'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => RiskCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'category_id' => Yii::t('backend', 'Категория'),
            'name' => Yii::t('backend', 'Название'),
            'description' => Yii::t('backend', 'Описание'),
            'sort_order' => Yii::t('backend', 'Порядок сортировки'),
            'params' => Yii::t('backend', 'Параметры'),
        ];
    }

    /**
     * Категория риска
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(RiskCategory::className(), ['id' => 'category_id']);
    }
}
