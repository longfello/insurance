<?php
return [
    'class'=>'common\components\MLUrlManager',
    'enablePrettyUrl'=>true,
    'showScriptName'=>false,
	'suffix' => '.html',
    'rules'=> [
        // Pages
        ['pattern'=>'page/<slug>', 'route'=>'page/view'],

        ['pattern'=>'api/<slug>/<action>', 'route'=>'api/<action>'],
        ['pattern'=>'payment-ipn', 'route'=>'payment/payment-ipn'],
        ['pattern'=>'validate-form', 'route'=>'validate/form'],
        ['pattern'=>'company/<id>', 'route'=>'site/company'],
	    ['pattern'=>'send-partner', 'route'=>'site/send-partner'],

        // Articles
        ['pattern'=>'article/index', 'route'=>'article/index'],
        ['pattern'=>'article/attachment-download', 'route'=>'article/attachment-download'],
        ['pattern'=>'article/<slug>', 'route'=>'article/view'],


        ['pattern'=>'t', 'route'=>'site/t'],
        ['pattern'=>'t2', 'route'=>'site/t2'],

        ['pattern'=>'<slug>', 'route'=>'page/landing'],
    ]
];
