<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error page__inner">
    <div class="site-error__wrap">

        <h1 class="site-error__title"><?php echo Html::encode($this->title) ?></h1>

        <div class="alert alert-danger site-error__alert">
            <?php echo nl2br(Html::encode($message)) ?>
        </div>

        <p class="site-error__text">
            The above error occurred while the Web server was processing your request.
        </p>
        <p class="site-error__text">
            Please contact us if you think this is a server error. Thank you.
        </p>

    </div>
</div>
