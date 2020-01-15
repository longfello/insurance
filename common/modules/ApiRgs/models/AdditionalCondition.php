<?php

namespace common\modules\ApiRgs\models;

use Yii;

/**
 * Доп. условия
 * This is the model class for table "api_rgs_additional_condition".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 * @property int $additional_condition_type_id
 * @property int $default
 */
class AdditionalCondition extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'additional_condition';

    /**
     * @inheritdoc
     */
    public $title_length = 75;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['additional_condition_type_id'], 'required'];
        $rules[] = [['additional_condition_type_id'], 'integer'];
        $rules[] = [['additional_condition_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => AdditionalConditionType::className(), 'targetAttribute' => ['additional_condition_type_id' => 'id']];
        $rules[] = [['default'], 'safe'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД доп. условия'),
            'ext_id' => Yii::t('backend', 'ИД доп. условия во внешней системе'),
            'title' => Yii::t('backend', 'Доп. условие'),
            'additional_condition_type_id' => Yii::t('backend', 'Вид доп. условия'),
            'default' => Yii::t('backend', 'Использовать по умолчанию')
        ];
    }

    /**
     * Вид доп. условия
     * 
     * @return AdditionalConditionType
     */
    public function getAdditionalConditionTypeModel() {
        return $this->hasOne(AdditionalConditionType::className(), ['id' => 'additional_condition_type_id']);
    }

}
