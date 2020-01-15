<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * @title Обычная статическая страница с визуальным редактором
 * @var $this \yii\web\View
 * @var $model \common\models\Page
 */
$this->title = $model->title;
?>
<div class="content page__inner page__inner_index">
    <?php echo $model->body ?>
</div>