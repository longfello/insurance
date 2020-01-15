<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo;

use Yii;
use common\modules\geo\components;
use common\modules\geo\models;

/**
 * Class Module модуль гео-позиционирования
 * @package common\modules\geo
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'common\modules\geo\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // initialize the module with the configuration loaded from config.php
        \Yii::configure($this, require(__DIR__ . '/config.php'));
    }
}
