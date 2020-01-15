<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
return [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'api/index',
    'components' => [
        'urlManager'=>require(__DIR__.'/_urlManager.php'),
    ]
];
