<?php
/**
 * @title Страница лендинга
 */
/* @var $this yii\web\View */
/* @var $model \common\models\Page */

use \common\components\Calculator\widgets\travel\CalculatorWidget;

$this->title = $model->title;

$model = isset($model)?$model:false;
?>
<div class="page__inner page__inner_landing">
    <div class="js-scroll-column page__left page__left_landing">
        <?php echo $model->body ?>
    </div>
    <div class="js-scroll-column page__right page__right_landing">
        <div class="insurance-find insurance-find_landing">
            <?= CalculatorWidget::widget(['layout' => CalculatorWidget::LAYOUT_LANDING]); ?>
        </div>
    </div>
</div>