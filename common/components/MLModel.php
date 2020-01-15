<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 10.07.17
 * Time: 14:23
 */

namespace common\components;


/**
 * Class MLModel Модель мультиязычной таблицы БД
 * @package common\components
 */
class MLModel extends \yii\db\ActiveRecord
{
    /**
     * @var array Перечень мультиязычных аттрибутов
     */
    public $MLattributes = [];
    /**
     * @var string Имя поля внешней связи таблицы мультиязычных аттрибутов с основной таблицей
     */
    public $MLfk = 'parent_id';

    /**
     * @inheritdoc
     *
     * @param string $name
     *
     * @return string
     */
    public function generateAttributeLabel($name)
    {
        $ret = parent::generateAttributeLabel($name);
        foreach ($this->MLattributes as $attr){
            foreach (\Yii::$app->params['availableLocales'] as $iso => $lang_name){
                $iso = substr($iso, 0, 2);
                $suffix   = ($iso == substr(\Yii::$app->sourceLanguage,0,2))?"":"_".$iso;
                $field    = $attr.$suffix;
                if ($field == $name){
                    $baseName = $this->getAttributeLabel($attr);
                    $ret = $baseName;
                    break 2;
                }
            }
        }
        return $ret;
    }

    /**
     * @inheritdoc
     * Добавление поведения мультиязычности
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['ml'] = [
                'class' => MLBehavior::className(),
                'langForeignKey'  => $this->MLfk,
                'defaultLanguage' => substr(\Yii::$app->sourceLanguage,0,2),
                'tableName'       => self::getTableSchema()->name."Lang",
                'attributes'      => $this->MLattributes,
            ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     * @return \omgdef\multilingual\MultilingualQuery
     */
    public static function find() {
        return new \omgdef\multilingual\MultilingualQuery(get_called_class());
    }
}