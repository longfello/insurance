<?php
$config = [
    'homeUrl'=>Yii::getAlias('@frontendUrl'),
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'site/index',
    'bootstrap' => ['maintenance', 'MLManager'],
    'modules' => [
        'user' => [
            'class' => 'frontend\modules\user\Module',
            //'shouldBeActivated' => true
        ],
        'agency' => [
            'class' => 'frontend\modules\agency\Module',
        ], 
    ],
    'components' => [
        'MLManager' => [
            'class' => 'common\components\MLManager'
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'github' => [
                    'class' => 'yii\authclient\clients\GitHub',
                    'clientId' => env('GITHUB_CLIENT_ID'),
                    'clientSecret' => env('GITHUB_CLIENT_SECRET')
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => env('FACEBOOK_CLIENT_ID'),
                    'clientSecret' => env('FACEBOOK_CLIENT_SECRET'),
                    'scope' => 'email,public_profile',
                    'attributeNames' => [
                        'name',
                        'email',
                        'first_name',
                        'last_name',
                    ]
                ]
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error'
        ],
        'maintenance' => [
            'class' => 'common\components\maintenance\Maintenance',
            'enabled' => function ($app) {
                return $app->keyStorage->get('frontend.maintenance') === 'enabled';
            }
        ],
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
        'user' => [
            'class'=>'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['/user/sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
        ],
    ]
];

if (YII_ENV_DEV) {
	$config['modules']['gii'] = [
		'class'=>'yii\gii\Module',
		'allowedIPs' => ['*'],
		'generators'=>[
			'crud'=>[
				'class'=>'yii\gii\generators\crud\Generator',
				'messageCategory'=>'frontend'
			]
		]
	];
}

return $config;
