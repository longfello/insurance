<?php
/**
 * @title Обычная статическая страница
 * @var $this \yii\web\View
 * @var $model \common\models\Page
 */
$this->title = $model->title;
?>
<div class="content page__inner page__inner_index">
    <?php echo $model->body ?>
</div>