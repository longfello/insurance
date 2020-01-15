<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 10.07.17
 * Time: 11:15
 */

namespace common\components;


use common\models\Domain;
use yii\base\Component;
use yii\web\HttpException;

/**
 * Class MLManager Менеджер мультиязычности
 * @package common\components
 */
class MLManager extends Component
{
    /**
     * @var Domain Модель текущего домена
     */
    public $model;

    /**
     * @inheritdoc
     * @throws HttpException
     */
    public function init(){
        $domain = Domain::findOne(['name' => \Yii::$app->request->hostName, 'enabled' => 1]);
        if (!$domain){
            $domain = Domain::findOne(['default' => 1]);
            if ($domain) {
                \Yii::$app->response->redirect('//'.$domain->name);
                \Yii::$app->end();
            } else {
                throw new HttpException(404, 'Default domain not exist in database.');
            }
        }
        $this->model = $domain;

        $languages = [];
        foreach($domain->languages as $language){
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