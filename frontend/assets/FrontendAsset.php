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
class FrontendAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
	    'lib/select2/dist/css/select2.min.css',
		//'lib/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css',
		'styles/font-awesome.min.css',
		'lib/ion.rangeSlider/css/ion.rangeSlider.css',
  		'styles/datepicker.min.css',
    	'styles/jquery.datepick.css',
		'lib/ytplayer/css/mb.YTPlayer.css',
		'lib/owlCarousel/css/owl.carousel.min.css',
		'styles/main.css',
    ];

    public $js = [
	    "lib/handlebars/handlebars.min.js",
		"lib/select2/dist/js/select2.full.min.js",
		//"lib/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js",
		//"lib/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js",
		"js/datepicker.min.js",
		"js/jquery.plugin.js",
		"js/datepick-mobile.js",
		"lib/ion.rangeSlider/js/ion.rangeSlider.min.js",
		"lib/simplebar/simplebar.js",
		"lib/ScrollToFixed/jquery-scrolltofixed-min.js",
		"lib/maskedInput/js/jquery.inputmask.bundle.min.js",
		"lib/maskedInput/js/phone.min.js",
		"js/jquery.validate.min.js",
    "lib/ytplayer/js/jquery.mb.YTPlayer.min.js",
		"js/common.js",
		"js/select.js",
		"js/svgIcons.js",
	    'js/ajax-reloader.js',
		'lib/owlCarousel/js/owl.carousel.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\assets\Html5shiv',
	    MomentAsset::class,
    ];
}
