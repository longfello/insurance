<?php

$config = [
    'homeUrl'=>Yii::getAlias('@apiUrl'),
    'controllerNamespace' => 'api\controllers',
    'defaultRoute' => 'api/index',
    'bootstrap' => ['maintenance'],
    'components' => [
        'errorHandler' => [
            'errorAction' => 'api/error'
        ],
        'rest' => [
            'class' => 'api\components\Rest\RestComponent',
        ],
        'maintenance' => [
            'class' => 'common\components\maintenance\Maintenance',
            'enabled' => function ($app) {
                return $app->keyStorage->get('frontend.maintenance') === 'enabled';
            }
        ],
        'request' => [
            'class' => 'api\components\Request',
            'cookieValidationKey' => env('FRONTEND_COOKIE_VALIDATION_KEY'),
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/',
                'domain' => COOKIE_DOMAIN,
            ],
        ],
        'response' => [
            'class' => 'api\components\Response',
            'format' => \api\components\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'user' => [
            'class'=>'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['/user/sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
        ],
    ],
    'on beforeRequest' => function () {
        if (!in_array(Yii::$app->request->pathInfo, [''])) {
            Yii::$app->catchAll = ['api/proceed'];
        }
    },

];

return $config;
