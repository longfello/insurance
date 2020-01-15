<?php
/**
 * @title Страница с заголовком
 * @var $this \yii\web\View
 * @var $model \common\models\Page
 */
$this->title = $model->title;
?>
<div class="content page__inner page__inner_index">
    <h1><?php echo $model->title ?></h1>
    <?php echo $model->body ?>
</div>