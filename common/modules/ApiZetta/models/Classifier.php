<?php

namespace common\modules\ApiZetta\models;

use Yii;

/**
 * Справочник
 * This is the model class for table "api_zetta_classifier".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 * @property string $class
 */
class Classifier extends \yii\db\ActiveRecord {

    /**
     * Префикс таблицы
     */
    const TABLE_PREFIX = 'api_zetta';

    /**
     * Максимальная длинна ext_id
     */
    const EXT_ID_LENGTH = 36;

    /**
     * @var string Постфикс таблицы
     */
    public static $table_postfix = 'classifier';

    /**
     * @var int Максимальная длинна заголовка
     */
    public $title_length = 35;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return self::TABLE_PREFIX . '_' . static::$table_postfix;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ext_id', 'title'], 'required'],
            [['ext_id'], 'string', 'max' => self::EXT_ID_LENGTH],
            [['title'], 'string', 'max' => $this->title_length],
            [['class'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД справочника'),
            'ext_id' => Yii::t('backend', 'ИД справочника во внешней системе'),
            'title' => Yii::t('backend', 'Справочник'),
            'class' => Yii::t('backend', 'Класс обработчика')
        ];
    }

}
