<?php

namespace common\modules\ApiZetta\models;

use Yii;

/**
 * Программа
 * This is the model class for table "api_zetta_program".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 */
class Program extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'program';

    /**
     * @inheritdoc
     */
    public $title_length = 5;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['priority', 'enabled'], 'integer'];

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
            'priority' => Yii::t('backend', 'Приоритет использования'),
            'enabled' => Yii::t('backend', 'Разрешена')
        ];
    }

    public function getRisks() {
        return ProgramRisk::find()->where(['program_id' => $this->id])->all();
    }

    public function getSums() {
        return ProgramSum::find()->where(['program_id' => $this->id])->all();
    }

}
