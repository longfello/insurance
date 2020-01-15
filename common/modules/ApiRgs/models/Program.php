<?php

namespace common\modules\ApiRgs\models;

use Yii;
use common\modules\ApiRgs\models\RiskType;
/**
 * Программа
 * This is the model class for table "api_rgs_program".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 * @property int $risk_type_id
 * @property int $enabled
 */
class Program extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'program';

    /**
     * @inheritdoc
     */
    public $title_length = 35;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['risk_type_id'], 'integer'];
        $rules[] = [['risk_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RiskType::className(), 'targetAttribute' => ['risk_type_id' => 'id']];
        $rules[] = [['enabled'], 'safe'];

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
            'risk_type_id' => Yii::t('backend', 'Вид риска'),
            'enabled' => Yii::t('backend', 'Разрешено')
        ];
    }

    /**
     * Вид риска
     * 
     * @return RiskType
     */
    public function getRiskType() {
        return $this->hasOne(RiskType::className(), ['id' => 'risk_type_id']);
    }

}
