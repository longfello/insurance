<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use kartik\daterange\DateRangePickerAsset;
use kartik\daterange\MomentAsset;
use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class NewFrontendAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'lib/select2/dist/css/select2.min.css',
        'lib/owlCarousel/css/owl.carousel.min.css',
        'styles/datepicker.min.css',
        'styles/main-new.css',
    ];

    public $js = [
        'lib/select2/dist/js/select2.full.min.js',
        'lib/owlCarousel/js/owl.carousel.min.js',
        'js/datepicker.min.js',
        'js/jquery.validate.min.js',
        'js/ajax-reloader.js',
        'js/common-new.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\assets\Html5shiv',
        MomentAsset::class,
    ];
}
