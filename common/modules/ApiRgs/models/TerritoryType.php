<?php

namespace common\modules\ApiRgs\models;

use Yii;

/**
 * Тип территории
 * This is the model class for table "api_rgs_territory_type".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 */
class TerritoryType extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'territory_type';

    /**
     * @inheritdoc
     */
    public $title_length = 120;

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД типа территории'),
            'ext_id' => Yii::t('backend', 'ИД типа территории во внешней системе'),
            'title' => Yii::t('backend', 'Тип территории')
        ];
    }

}
