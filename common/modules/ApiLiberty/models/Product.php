<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;

/**
 * Продукты
 * This is the model class for table "api_liberty_product".
 *
 * @property integer $productId
 * @property string $productName
 * @property string $rule_base_url
 * @property string $rule_path
 * @property string $police_base_url
 * @property string $police_path
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @var array
     */
    public $rule;
    /**
     * @var array
     */
    public $police;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productId', 'productName'], 'required'],
            [['productId'], 'integer'],
            [['productName'], 'string', 'max' => 255],
            [['rule', 'police'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'productId' => 'Ид продукта',
            'productName' => 'Название продукта',
            'rule' => Yii::t('backend', 'Правила страхования'),
            'police' => Yii::t('backend', 'Образец полиса'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'rule',
                'pathAttribute' => 'rule_path',
                'baseUrlAttribute' => 'rule_base_url'
            ],
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'police',
                'pathAttribute' => 'police_path',
                'baseUrlAttribute' => 'police_base_url'
            ]
        ];
    }
}
