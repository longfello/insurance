<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 10.07.17
 * Time: 14:19
 */

namespace common\components;


use omgdef\multilingual\MultilingualBehavior;

/**
 * Class MLBehavior Поведение мультиязічности
 * @package common\components
 */
class MLBehavior extends MultilingualBehavior
{
    /**
     * @inheritdoc
     */
    public function init(){
        parent::init();
        if (empty($this->languages) || !is_array($this->languages)) {
            $this->languages = \Yii::$app->params['availableLocales'];
        }
    }

}