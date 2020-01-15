<?php

namespace common\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;

/**
 * Файлы API
 * This is the model class for table "api_files".
 *
 * @property integer $id
 * @property integer $api_id
 * @property string $name
 * @property string $file_base_url
 * @property string $file_path
 *
 * @property Api $api
 */
class ApiFiles extends \yii\db\ActiveRecord
{
	/**
     * Файл
	 * @var array
	 */
	public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_id', 'name'], 'required'],
            [['api_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['file_base_url', 'file_path'], 'string', 'max' => 1024],
            [['api_id'], 'exist', 'skipOnError' => true, 'targetClass' => Api::className(), 'targetAttribute' => ['api_id' => 'id']],
	        [['file'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'api_id' => 'Api ID',
            'name' => 'Название файла',
            'file_base_url' => 'Файл',
            'file_path' => 'File Path',
            'file' => 'Файл',
        ];
    }

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			[
				'class' => UploadBehavior::className(),
				'attribute' => 'file',
				'pathAttribute' => 'file_path',
				'baseUrlAttribute' => 'file_base_url'
			]
		];
	}



	/**
     * API
     * @return \yii\db\ActiveQuery
     */
    public function getApi()
    {
        return $this->hasOne(Api::className(), ['id' => 'api_id']);
    }
}
