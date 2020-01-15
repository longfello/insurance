<?php

namespace common\modules\ApiZetta\models;

use Yii;

/**
 * Риск
 * This is the model class for table "api_zetta_risk".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 */
class Risk extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'risk';

    /**
     * @inheritdoc
     */
    public $title_length = 100;

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД риска'),
            'ext_id' => Yii::t('backend', 'ИД риска во внешней системе'),
            'title' => Yii::t('backend', 'Риск')
        ];
    }

}
