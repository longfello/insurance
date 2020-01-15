<?php
/**
 * @var $this \yii\web\View
 * @var $page \common\models\Page
 * @var $type \common\models\InsuranceType
 * @var $widget string
 * @var $availableTypes \common\models\InsuranceType[]
 *
 */

use \common\components\Calculator\widgets\travel\CalculatorWidget;

?>
<div class="insurance-find__type" id="calc-<?= $type->slug ?>">
    <div class="insurance-find-form insurance-find-form_<?= $type->slug ?>">
        <?= CalculatorWidget::widget(['layout' => CalculatorWidget::LAYOUT_HOME_NEW]); ?>
    </div>
</div>
