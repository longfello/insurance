<?php
/**
 * @var $this \yii\web\View
 * @var $page \common\models\Page
 * @var $type \common\models\InsuranceType
 * @var $widget string
 * @var $availableTypes \common\models\InsuranceType[]
 *
 */

?>

<?php if ($widget) { ?>
    <div class="insurance-find__type insurance-find__type_landing active" id="calc-<?= $type->slug ?>">
        <div class="insurance-find-form insurance-find-form_landing insurance-find-form_<?= $type->slug ?>">
            <?= $widget ?>
        </div>
    </div>
<?php } ?>