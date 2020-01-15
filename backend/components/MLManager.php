<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 10.07.17
 * Time: 11:15
 */

namespace backend\components;


use common\models\Domain;
use common\models\Languages;
use yii\base\Component;
use yii\web\HttpException;

/**
 * Class MLManager менеджер языков мультиязычности
 * @package backend\components
 */
class MLManager extends Component
{
    /**
     * @inheritdoc
     */
    public function init(){
        $query = "SELECT DISTINCT language_id FROM domain2language";
        $ids = \Yii::$app->db->createCommand($query)->queryColumn();
        $models = Languages::findAll(['id' => $ids]);
        $languages = [];
        foreach($models as $language){
            $languages[$language->getAttribute('iso_639-1')] = $language->name.' ('.$language->getAttribute('iso_639-1').')';
        }
        if (!$languages) {
            $languages = [
                \Yii::$app->language => 'Default'
            ];
        }
        \Yii::$app->params['availableLocales'] = $languages;
    }
}