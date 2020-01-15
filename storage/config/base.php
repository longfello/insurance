<?php
/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
return [
    'id' => 'storage',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'glide/index',
    'controllerMap' => [
        'glide' => '\trntv\glide\controllers\GlideController'
    ],
    'components' => [
        'request' => [
            'class' => 'yii\web\Request',
            'cookieValidationKey' => env('FRONTEND_COOKIE_VALIDATION_KEY'),
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/',
                'domain' => COOKIE_DOMAIN,
            ],
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'cookieParams' => [
                'httpOnly' => true,
                'path' => '/',
                'domain' => COOKIE_DOMAIN,
            ]
        ],
        'urlManager'=>require(__DIR__.'/_urlManager.php'),
        'glide' => [
            'class' => 'trntv\glide\components\Glide',
            'sourcePath' => '@storage/web/source',
            'cachePath' => '@storage/cache',
            'maxImageSize' => env('GLIDE_MAX_IMAGE_SIZE'),
            'signKey' => env('GLIDE_SIGN_KEY')
        ]
    ]
];
