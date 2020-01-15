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
                'label' => Yii::t('backend', 'Timeline'),
                'icon' => '<i class="fa fa-bar-chart-o"></i>',
                'url' => ['/timeline-event/index'],
                'badge' => \common\models\TimelineEvent::find()->today()->count(),
                'badgeBgClass' => 'label-success',
            ],
            \common\models\Api::getAdminMenu(),
            [
                'label' => Yii::t('backend', 'Справочники'),
                'url' => '#',
                'icon' => '<i class="fa fa-address-book"></i>',
                'options' => ['class' => 'treeview'],
                'items' => [
                    ['label' => Yii::t('backend', 'Страны'), 'url' => ['/geo-country/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Города'), 'url' => ['/geo-name/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Интервалы страховых сумм'), 'url' => ['/cost-interval/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Категории рисков'), 'url' => ['/risk-category/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Риски'), 'url' => ['/risk/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Дополнительные условия'), 'url' => ['/additional-condition/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Типы страхования'), 'url' => ['/insurance-type/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Фильтр'), 'url' => '#', 'icon' => '<i class="fa fa-angle-double-right"></i>', 'options' => ['class' => 'treeview'], 'items' => [
                        ['label' => Yii::t('backend', 'Готовые решения'), 'url' => ['/filter-solution/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                        ['label' => Yii::t('backend', 'Параметры'), 'url' => ['/filter-param/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ]],
                    ['label' => Yii::t('backend', 'API'), 'url' => ['/api/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Валюты'), 'url' => ['/currency/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ],

            ],
            [
                'label' => Yii::t('backend', 'Content'),
                'url' => '#',
                'icon' => '<i class="fa fa-edit"></i>',
                'options' => ['class' => 'treeview'],
                'items' => [
                    ['label' => Yii::t('backend', 'Static pages'), 'url' => ['/page/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Landings'), 'url' => ['/landing/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Articles'), 'url' => ['/article/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Article Categories'), 'url' => ['/article-category/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Text Widgets'), 'url' => ['/widget-text/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Menu Widgets'), 'url' => ['/widget-menu/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Carousel Widgets'), 'url' => ['/widget-carousel/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                ],
            ],
            [
                'label' => 'Заказы',
                'url' => '/orders/index',
                'icon' => '<i class="fa fa-plus"></i>',
            ],
            [
                'label' => Yii::t('backend', 'System'),
                'options' => ['class' => 'header'],
                'visible' => Yii::$app->user->can('administrator')
            ],
            [
                'label' => Yii::t('backend', 'Users'),
                'icon' => '<i class="fa fa-users"></i>',
                'url' => ['/user/index'],
                'visible' => Yii::$app->user->can('administrator')
            ],
            [
                'label' => Yii::t('backend', 'Прочее'),
                'url' => '#',
                'icon' => '<i class="fa fa-cogs"></i>',
                'options' => ['class' => 'treeview'],
                'items' => [
                    [
                        'label' => Yii::t('backend', 'i18n'),
                        'url' => '#',
                        'icon' => '<i class="fa fa-flag"></i>',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => Yii::t('backend', 'i18n Source Message'), 'url' => ['/i18n/i18n-source-message/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                            ['label' => Yii::t('backend', 'i18n Message'), 'url' => ['/i18n/i18n-message/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                        ]
                    ],
                    ['label' => Yii::t('backend', 'Key-Value Storage'), 'url' => ['/key-storage/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'File Storage'), 'url' => ['/file-storage/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'Cache'), 'url' => ['/cache/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => Yii::t('backend', 'File Manager'), 'url' => ['/file-manager/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    ['label' => 'Домены', 'url' => ['/domain/index'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                    [
                        'label' => Yii::t('backend', 'System Information'),
                        'url' => ['/system-information/index'],
                        'icon' => '<i class="fa fa-angle-double-right"></i>'
                    ],
                    [
                        'label' => Yii::t('backend', 'Logs'),
                        'url' => ['/log/index'],
                        'icon' => '<i class="fa fa-angle-double-right"></i>',
                        'badge' => \backend\models\SystemLog::find()->count(),
                        'badgeBgClass' => 'label-danger',
                    ],
                ]
            ]
        ]
    ]);
} catch (Exception $e) {
    echo("Во время построения меню возникла ошибка");
}
