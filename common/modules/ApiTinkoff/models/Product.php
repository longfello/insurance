<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;
use trntv\filekit\behaviors\UploadBehavior;
use common\components\SerializeBehavior;
/**
 * Продукты
 * This is the model class for table "api_tinkoff_product".
 *
 * @property integer $id
 * @property string $Name
 * @property string $ProductType
 * @property double $ProductVersion
 * @property string $ShortDescription
 * @property string $FullDescription
 * @property string $AssistanceLevel
 * @property string $Currency
 * @property string $rule_base_url
 * @property string $rule_path
 * @property string $police_base_url
 * @property string $police_path
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @var array файл правил
     */
    public $rule;
    /**
     * @var array файл примера полиса
     */
    public $police;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ProductType', 'ShortDescription', 'FullDescription'], 'required'],
            [['ProductVersion'], 'number'],
            [['FullDescription'], 'string'],
            [['Name', 'ProductType'], 'string', 'max' => 64],
            [['ShortDescription'], 'string', 'max' => 512],
            [['rule', 'police'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Name' => 'Название продукта',
            'ProductType' => 'Тип продукта',
            'ProductVersion' => 'Версия продукта',
            'ShortDescription' => 'Краткое описание продукта',
            'FullDescription' => 'Полное описание продукта',
            'AssistanceLevel' => 'Уровень поддержки',
            'Currency' => 'Валюта страхования',
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
            ],
            [
                'class' => SerializeBehavior::className(),
                'attributes' => ['AssistanceLevel', 'Currency'],
                'encode' => true,
            ]
        ];
    }
}
