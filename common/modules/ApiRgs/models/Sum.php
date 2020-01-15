<?php

namespace common\modules\ApiRgs\models;

use Yii;
use common\components\Calculator\forms\TravelForm;

/**
 * Суммы страхования
 * This is the model class for table "api_rgs_sum".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 * @property int $sum
 * @property int $program_id
 * @property int $enabled
 * @property int $manual
 */
class Sum extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'sum';

    /**
     * @inheritdoc
     */
    public $title_length = 12;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['sum', 'program_id'], 'required'];
        $rules[] = [['sum', 'program_id', 'enabled', 'manual'], 'integer'];
        $rules[] = [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД суммы'),
            'ext_id' => Yii::t('backend', 'ИД суммы во внешней системе'),
            'title' => Yii::t('backend', 'Сумма страхования (текстом)'),
            'sum' => Yii::t('backend', 'Сумма страхования'),
            'program_id' => Yii::t('backend', 'Программа'),
            'enabled' => Yii::t('backend', 'Разрешена'),
            'manual' => Yii::t('backend', 'Задается вручную')
        ];
    }

    /**
     * Программа
     * 
     * @return Program
     */
    public function getProgramModel() {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    /**
     * Риски в виде массива
     * @todo Допилить вывод рисков
     * 
     * @param TravelForm $form
     *
     * @return array
     */
    public function getRisksAsArray(TravelForm $form) {
        $risks = ['Страховая сумма' => $this->sum];

        $program = $this->programModel;
        $riskType = $program->riskType;
        $risks[$riskType->title . ' - ' . $program->title] = 1;

        return $risks;
    }

}
