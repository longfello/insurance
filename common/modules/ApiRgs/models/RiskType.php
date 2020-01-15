<?php

namespace common\modules\ApiRgs\models;

use Yii;

/**
 * Риск
 * This is the model class for table "api_rgs_risk_type".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 * @property int $main
 */
class RiskType extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'risk_type';

    /**
     * @inheritdoc
     */
    public $title_length = 50;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['main'], 'integer'];
        $rules[] = [['main'], 'safe'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД вида риска'),
            'ext_id' => Yii::t('backend', 'ИД вида риска во внешней системе'),
            'title' => Yii::t('backend', 'Вид риска'),
            'main' => Yii::t('backend', 'Основной')
        ];
    }

}
