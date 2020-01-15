<?php
return [
    'id' => 'frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['location'],
    'components' => [
        'urlManager' => require(__DIR__.'/_urlManager.php'),
        'cache' => require(__DIR__.'/_cache.php'),
        'location' => [
            'class' => '\common\modules\geo\components\location'
        ],
        'assetManager'=>array(
	        'bundles' => array(
		        'yii\web\JqueryAsset' => array(
			        'sourcePath' => null,
			        'js' => array(
				        '//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js',
			        ),
		        ),
	        ) 
        )
    ],
];
