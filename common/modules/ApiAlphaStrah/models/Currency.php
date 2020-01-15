<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;

/**
 * Валюты - справочник АПИ
 * This is the model class for table "currency".
 *
 * @property integer $currencyID
 * @property string $currency
 * @property string $currencyName
 */
class Currency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_currency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currencyID', 'currency', 'currencyName'], 'required'],
            [['currencyID'], 'integer'],
            [['currency'], 'string', 'max' => 3],
            [['currencyName'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'currencyID' => Yii::t('backend', 'ID'),
            'currency' => Yii::t('backend', 'ISO-код валюты'),
            'currencyName' => Yii::t('backend', 'Название валюты'),
        ];
    }
}
