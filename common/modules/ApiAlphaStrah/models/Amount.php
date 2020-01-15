<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;

/**
 * Страховая сумма
 * This is the model class for table "api_alpha_amount".
 *
 * @property integer $id
 * @property integer $amount
 */
class Amount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_amount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount'], 'required'],
            [['amount'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => 'Сумма',
        ];
    }

}
