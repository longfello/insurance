<?php

namespace common\modules\ApiZetta\models;

use Yii;
use trntv\filekit\behaviors\UploadBehavior;

/**
 * Продукты
 * This is the model class for table "api_zetta_product".
 *
 * @property integer $id
 * @property string $ext_id
 * @property string $title
 * @property string $rule_path
 * @property string $rule_base_url
 * @property string $police_path
 * @property string $police_base_url
 */
class Product extends Classifier {

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
    public static $table_postfix = 'product';

    /**
     * @inheritdoc
     */
    public $title_length = 10;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['rule', 'police'], 'safe'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД программы'),
            'ext_id' => Yii::t('backend', 'ИД программы во внешней системе'),
            'title' => Yii::t('backend', 'Программа'),
            'rule' => Yii::t('backend', 'Правила страхования'),
            'police' => Yii::t('backend', 'Образец полиса')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
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
