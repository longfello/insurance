<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 19.12.17
 * Time: 13:26
 */

use backend\widgets\Menu;

try {
    echo Menu::widget([
        'options' => ['class' => 'sidebar-menu'],
        'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
        'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
        'activateParents' => true,
        'items' => [
            [
                'label' => Yii::t('backend', 'Main'),
                'options' => ['class' => 'header'],
            ],
            [
                'label' => 'Заказы',
                'url' => '/api-orders/index',
                'icon' => '<i class="fa fa-plus"></i>',
                'visible' => Yii::$app->user->can('api_user') || Yii::$app->user->can('test_api_user')

            ],
        ]
    ]);
} catch (Exception $e) {
    echo("Во время построения меню возникла ошибка");
}
