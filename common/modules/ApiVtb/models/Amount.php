<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use Yii;

/**
 * Страховые суммы
 * This is the model class for table "api_vtb_amount".
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
        return 'api_vtb_amount';
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
