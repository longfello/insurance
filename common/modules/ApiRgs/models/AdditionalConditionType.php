<?php

namespace common\modules\ApiRgs\models;

use Yii;

/**
 * Виды доп. условий
 * This is the model class for table "api_rgs_additional_condition_type".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 */
class AdditionalConditionType extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'additional_condition_type';

    /**
     * @inheritdoc
     */
    public $title_length = 45;

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД вида доп. условия'),
            'ext_id' => Yii::t('backend', 'ИД вида доп. условия во внешней системе'),
            'title' => Yii::t('backend', 'Вид доп. условия')
        ];
    }

}
