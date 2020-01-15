<?php

namespace common\models;

use common\components\MLBehavior;
use common\components\MLModel;
use Yii;

/**
 * Языки
 * This is the model class for table "languages".
 *
 * @property integer $id
 * @property string $name
 * @property string $iso_639-1
 */
class Languages extends MLModel
{
    /**
     * @inheritdoc
     */
    public $MLattributes = ['name'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'languages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 49],
            [['iso_639-1'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'iso_639-1' => 'Iso 639 1',
        ];
    }
}
