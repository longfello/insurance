<?php

namespace common\modules\ApiRgs\models;

use Yii;

/**
 * Валюта
 * This is the model class for table "api_rgs_currency".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 * @property int $default
 * @property int $enabled
 */
class Currency extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'currency';

    /**
     * @inheritdoc
     */
    public $title_length = 3;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['default', 'enabled'], 'integer'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД валюты'),
            'ext_id' => Yii::t('backend', 'ИД валюты во внешней системе'),
            'title' => Yii::t('backend', 'Валюта'),
            'default' => Yii::t('backend', 'По умолчанию'),
            'enabled' => Yii::t('backend', 'Разрешена')
        ];
    }

    public static function getDefault() {
        return self::findOne(['default' => 1]);
    }

}
