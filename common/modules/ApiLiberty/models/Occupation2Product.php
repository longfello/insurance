<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Соответствие опций программам
 * This is the model class for table "api_liberty_occupation2product".
 *
 * @property integer $id
 * @property integer $productId
 */
class Occupation2Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_occupation2product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'productId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Ид опции',
            'productId' => 'Ид продукта',
        ];
    }
}
