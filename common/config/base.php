<?php
$config = [
    'name'=>'Bullo Safe',
    'vendorPath'=>dirname(dirname(__DIR__)).'/vendor',
    'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'sourceLanguage'=>'en-US',
    'language'=>'ru-RU',
    'bootstrap' => ['log'],
    'components' => [
    	'payu' => [
		    'class' => '\common\components\PayU',
		    'merchantId' => getenv('PAYU_MERCHANT_ID'),
		    'merchantName' => getenv('PAYU_MERCHANT_NAME'),
		    'secretKey' => getenv('PAYU_SECRET_KEY'),
		    'debug' => getenv('PAYU_DEBUG'),
	    ],
	    'CbRF' => [
		    'class' => 'microinginer\CbRFRates\CBRF',
		    'defaultCurrency' => "EUR"
	    ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => '{{%rbac_auth_item}}',
            'itemChildTable' => '{{%rbac_auth_item_child}}',
            'assignmentTable' => '{{%rbac_auth_assignment}}',
            'ruleTable' => '{{%rbac_auth_rule}}'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@common/runtime/cache'
        ],

        'commandBus' => [
            'class' => 'trntv\bus\CommandBus',
            'middlewares' => [
                [
                    'class' => '\trntv\bus\middlewares\BackgroundCommandMiddleware',
                    'backgroundHandlerPath' => '@console/yii',
                    'backgroundHandlerRoute' => 'command-bus/handle',
                ]
            ]
        ],

        'formatter'=>[
            'class'=>'yii\i18n\Formatter',
            'thousandSeparator' => ',',
            'decimalSeparator' => '.',
        ],

        'glide' => [
            'class' => 'trntv\glide\components\Glide',
            'sourcePath' => '@storage/web/source',
            'cachePath' => '@storage/cache',
            'urlManager' => 'urlManagerStorage',
            'maxImageSize' => env('GLIDE_MAX_IMAGE_SIZE'),
            'signKey' => env('GLIDE_SIGN_KEY')
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
	            'class' => 'Swift_SmtpTransport',
	            'host'         => env('MAILER_HOST'),
                'username'     => env('MAILER_USERNAME'),
                'password'     => env('MAILER_PASSWORD'),
	            'port'         => env('MAILER_PORT'),
	            'encryption'   => env('MAILER_ENCRYPT'),
            ],
            //'useFileTransport' => true,
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => env('ADMIN_EMAIL')
            ]
        ],

        'db'=>[
            'class'=>'yii\db\Connection',
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'tablePrefix' => env('DB_TABLE_PREFIX'),
            'charset' => 'utf8',
            'enableSchemaCache' => YII_ENV_PROD,
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'db'=>[
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except'=>['yii\web\HttpException:*', 'yii\i18n\I18N\*'],
                    'prefix'=>function () {
                        $url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                        return sprintf('[%s][%s]', Yii::$app->id, $url);
                    },
                    'logVars'=>[],
                    'logTable'=>'{{%system_log}}'
                ],
                'Tinkoff' => [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['info'],
                    'categories' => ['bull_*'],
                    'prefix'=>function ($message) {
                        return sprintf('%s', strtok($message[0], PHP_EOL));
                    },
                    'logVars'=>[],
                    'logTable'=>'{{%api_tinkoff_log}}'
                ],
            ],
        ],

        'i18n' => [
            'translations' => [
            	/*
                'app'=>[
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath'=>'@common/messages',
                ],
                '*'=> [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath'=>'@common/messages',
                    'fileMap'=>[
                        'common'=>'common.php',
                        'backend'=>'backend.php',
                        'frontend'=>'frontend.php',
                    ],
                    'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
                ],
            	*/
                /* Uncomment this code to use DbMessageSource */
                 '*'=> [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceMessageTable'=>'{{%i18n_source_message}}',
                    'messageTable'=>'{{%i18n_message}}',
                    'enableCaching' => YII_ENV_DEV,
                    'cachingDuration' => 3600,
                    'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
                ],
//                */
            ],
        ],

        'fileStorage' => [
            'class' => '\trntv\filekit\Storage',
            'baseUrl' => '@storageUrl/source',
            'filesystem' => [
                'class' => 'common\components\filesystem\LocalFlysystemBuilder',
                'path' => '@storage/web/source'
            ],
            'as log' => [
                'class' => 'common\behaviors\FileStorageLogBehavior',
                'component' => 'fileStorage'
            ]
        ],

        'keyStorage' => [
            'class' => 'common\components\keyStorage\KeyStorage'
        ],

        'urlManagerBackend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => Yii::getAlias('@backendUrl')
            ],
            require(Yii::getAlias('@backend/config/_urlManager.php'))
        ),
        'urlManagerFrontend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => Yii::getAlias('@frontendUrl')
            ],
            require(Yii::getAlias('@frontend/config/_urlManager.php'))
        ),
        'urlManagerStorage' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo'=>Yii::getAlias('@storageUrl')
            ],
            require(Yii::getAlias('@storage/config/_urlManager.php'))
        ),
        'urlManagerApi' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo'=>Yii::getAlias('@apiUrl'),
                'baseUrl' => '/'
            ],
            require(Yii::getAlias('@api/config/_urlManager.php'))
        )
    ],
	'modules' => [
		'ApiErv' => [
			'class' => 'common\modules\ApiErv\Module',
		],
		'ApiVtb' => [
			'class' => 'common\modules\ApiVtb\Module',
		],
		'ApiAlphaStrah' => [
			'class' => 'common\modules\ApiAlphaStrah\Module',
		],
        'ApiLiberty' => [
            'class' => 'common\modules\ApiLiberty\Module',
        ],
        'ApiTinkoff' => [
            'class' => 'common\modules\ApiTinkoff\Module',
        ],
        'ApiSberbank' => [
            'class' => 'common\modules\ApiSberbank\Module',
        ],
        'ApiZetta' => [
            'class' => 'common\modules\ApiZetta\Module'
        ],
        'ApiRgs' => [
            'class' => 'common\modules\ApiRgs\Module'
        ],
		'geo' => [
			'class' => 'common\modules\geo\Module',
		],
	],
    'params' => [
        'adminEmail' => env('ADMIN_EMAIL'),
        'robotEmail' => env('ROBOT_EMAIL'),
        'languages'       => [
            'en-US'=>'English (En)',
            'ru-RU'=>'Русский (Ru)',
            'kz-KZ'=>'Қазақ (Kz)',
        ],
        'availableLocales'=>[
            'en-US'=>'English (En)',
            'ru-RU'=>'Русский (Ru)',
            'kz-KZ'=>'Қазақ (Kz)',
        ],
        'maskMoneyOptions' => [
	        'prefix' => '',
	        'suffix' => '',
	        'affixesStay' => true,
	        'thousands' => ',',
	        'decimal' => '.',
	        'precision' => 2,
	        'allowZero' => true,
	        'allowNegative' => false,
        ]
    ],
];

if (YII_ENV_PROD) {
    $config['components']['log']['targets']['email'] = [
        'class' => 'yii\log\EmailTarget',
        'except' => ['yii\web\HttpException:*'],
        'levels' => ['error', 'warning'],
        'message' => ['from' => env('ROBOT_EMAIL'), 'to' => env('ADMIN_EMAIL')]
    ];
}

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
    ];

    $config['components']['cache'] = [
        'class' => 'yii\caching\DummyCache'
    ];
    /*
    $config['components']['mailer']['transport'] = [
        'class' => 'Swift_SmtpTransport',
        'host' => env('SMTP_HOST'),
        'port' => env('SMTP_PORT'),
    ];
    */
}

return $config;
