<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Страховая сумма дополнительных рисков
 * This is the model class for table "api_liberty_summ2cost".
 *
 * @property integer $summ_id
 * @property string $name
 * @property integer $amount
 */
class Summ2Cost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_summ2cost';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['summ_id', 'name', 'amount'], 'required'],
            [['summ_id', 'amount'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['summ_id'], 'exist', 'skipOnError' => true, 'targetClass' => Summ::className(), 'targetAttribute' => ['summ_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'summ_id' => 'Id суммы',
            'name' => 'Название риска',
            'amount' => 'Страховая сумма'
        ];
    }
}
