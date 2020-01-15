<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Соответствие территорий продуктам
 * This is the model class for table "api_liberty_territory2product".
 *
 * @property integer $id_area
 * @property integer $productId
 */
class Territory2Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_territory2product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_area', 'productId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_area' => 'Ид территории',
            'productId' => 'Ид продукта',
        ];
    }
}
