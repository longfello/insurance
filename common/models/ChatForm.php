<?php
namespace common\models;

use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\Url;


/**
 * Chat form
 *
 */
class ChatForm extends Model
{

    /**
     * @var  string  Сообщение
     */
    public $text;

    /**
     * @var
     */
    public $doc;


    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['text'], 'required'],
            [['text'], 'filter', 'filter' => 'trim'],
            [['text'], 'string', 'min' => 2, 'max' => 255],
            [['doc'], 'file',
                'extensions' => ['pdf','csv','doc','xls','docx','xlsx','txt'],
                'maxSize' => 1024 * 1024 * 5,
                'checkExtensionByMimeType' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [

            'text'=>'Сообщение',
            'doc'=>'Добавить документ'
        ];
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function download($key=false)
    {
        if ($this->validate() && $this->doc) {

            $file_doc = $this->doc->baseName .'.'. $this->doc->extension;

            $this->doc->saveAs(Yii::getAlias('@storage').'/web/chat_files/'.$key.'_'. $file_doc);

            return true;
        } else {
            return false;
        }
    }


}
