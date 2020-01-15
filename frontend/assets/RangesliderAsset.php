<?php
namespace frontend\assets;

use yii\web\AssetBundle;

class RangesliderAsset extends AssetBundle
{
    //public $basePath = '@webroot';
    //public $baseUrl = '@web';
    public $sourcePath = '@frontend/web';

    public $css = [
        'lib/ion.rangeSlider/css/ion.rangeSlider.css'
    ];

    public $js = [
        "lib/ion.rangeSlider/js/ion.rangeSlider.min.js",
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}