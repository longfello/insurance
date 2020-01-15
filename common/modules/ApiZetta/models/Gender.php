<?php

namespace common\modules\ApiZetta\models;

use Yii;

/**
 * Пол
 * This is the model class for table "api_zetta_gender".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 * @property string $alias
 */
class Gender extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'gender';

    /**
     * @inheritdoc
     */
    public $title_length = 7;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['alias'], 'in', 'range' => ['male', 'female']];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД пола'),
            'ext_id' => Yii::t('backend', 'ИД пола во внешней системе'),
            'title' => Yii::t('backend', 'Пол'),
            'alias' => Yii::t('backend', 'Значение')
        ];
    }

}
