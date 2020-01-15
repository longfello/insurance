<?php
use yii\helpers\Html;
/* @var $this \yii\web\View */
/* @var $content string */

\frontend\assets\FrontendAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?php echo Yii::$app->language ?>">
    <head>
        <meta charset="<?php echo Yii::$app->charset ?>"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="format-detection" content="telephone=no">
        <meta name="theme-color" content="#ffffff">
        <link rel="icon" sizes="16x16" href="/img/favicon.png">
        <title><?php echo Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <?php echo Html::csrfMetaTags() ?>
        <?=$this->render('@frontend/views/layouts/partial/web-analytics.php');?>
        
    <style>
        body{
            color:#fff;
        }
    </style>
    </head>
    <body class="js-page page">
    <?php $this->beginBody() ?>
        <div class="container">
        <h1><?php echo 'layout for agency'; ?></h1>
        <?php echo $content ?>
        </div>
    <?php $this->endBody() ?>

    </body>
    </html>
<?php $this->endPage() ?>