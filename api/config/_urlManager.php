<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl'=>true,
    'showScriptName'=>false,
    'rules'=> [
        '' => 'api/index',
        '/route' => 'api/route',
    ]
];
