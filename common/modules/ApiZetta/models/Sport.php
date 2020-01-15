<?php

namespace common\modules\ApiZetta\models;

use Yii;

/**
 * Спорт
 * This is the model class for table "api_zetta_sport".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 */
class Sport extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'sport';

    /**
     * @inheritdoc
     */
    public $title_length = 8;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['enabled'], 'integer'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД спортивной программы'),
            'ext_id' => Yii::t('backend', 'ИД спортивной программы во внешней системе'),
            'title' => Yii::t('backend', 'Спортивная программа'),
            'enabled' => Yii::t('backend', 'Разрешена')
        ];
    }

}
