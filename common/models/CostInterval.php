<?php

namespace common\models;

use Yii;

/**
 * Интервалы стоимостей
 * This is the model class for table "cost_interval".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $from
 * @property integer $to
 */
class CostInterval extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cost_interval';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'from', 'to'], 'required'],
            [['from', 'to'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'name' => Yii::t('backend', 'Название интервала'),
            'from' => Yii::t('backend', 'Начальная сумма'),
            'to' => Yii::t('backend', 'Конечная сумма'),
            'description' => Yii::t('backend', 'Описание'),
        ];
    }
}
