<?php
/**
 * Copyright (c) kvk-group 2018.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 03.01.18
 * Time: 17:26
 *
 * @var $this \yii\web\View
 * @var $order \common\models\Orders
 */
?>

<?php $this->beginContent('@api/views/layouts/api.php') ?>
<h1>Данная страница более недоступна</h1>
<p>Сейчас вы будете переадресованы на гланую страницу</p>
<p>Если этого не происходит, нажмите <a class="btn btn-primary" href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl('site/index', true) ?>">эту кнопку для перехода на сайт</a></p>

<script type="text/javascript">
    setTimeout(function(){
        document.location.href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl('site/index', true) ?>";
    }, 3000);
</script>
<?php $this->endContent() ?>
