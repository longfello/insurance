<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 23.12.16
 * Time: 14:58
 */
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use \yii\helpers\ArrayHelper;

use common\modules\ApiLiberty\models\Summ2Cost;
use common\components\Calculator\models\travel\FilterParam;

/* @var $this yii\web\View */

$this->title = 'Страховые суммы для '.$risk_model->riskName.' ('.$amount.')';
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Риски'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $risk_model['riskName'], 'url' => ['view','id'=>$risk_model['riskId']]];
$this->params['breadcrumbs'][] = 'Страховые суммы';
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="form-group field-price-incs">
        <div class="container-fluid">
            <div class="col-xs-1"></div>
            <div class="col-xs-3">Страховая сумма</div>
            <div class="col-xs-8">Риск <a class="btn btn-success pull-right js-add-risk" href="#">Добавить риск</a></div>
        </div>

        <div class="container-fluid risks-wrapper">
            <?php
            $incs = Summ2Cost::find()->where(['summ_id' => $summ->id])->orderBy( [ 'name' => SORT_ASC ] )->all();
            foreach ( $incs as $inc ) {
                /** @var $inc Summ2Cost */
                ?>
                <div class="col-wrapper">
                    <div class="col-xs-1">
                        <a class="js-remove-column btn btn-sm btn-danger">X</a>
                    </div>
                    <div class="col-xs-3">
                        <input type="number" name="cost_amount[]" value="<?= $inc->amount ?>" class="form-control">
                    </div>
                    <div class="col-xs-8">
                        <?= Html::textInput('cost_name[]', $inc->name, ['class'=>"form-control"]) ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Html::submitButton('Сохранить', [ 'class' => 'btn btn-primary' ] ) ?>
    </div>
<?php ActiveForm::end(); ?>

<div id="append_source" class="hidden">
    <div class="col-wrapper">
        <div class="col-xs-1"><a class="js-remove-column btn btn-sm btn-danger">X</a></div>
        <div class="col-xs-3"><input type="number" name="cost_amount[]"  class="form-control"></div>
        <div class="col-xs-8"><input type="text" name="cost_name[]" class="form-control"></div>
        <div class="clearfix"></div>
    </div>
</div>

<?php $this->registerJs("
$(document).on('click', '.js-remove-column', function(e){
  e.preventDefault();
  $(this).parents('.col-wrapper').remove();
});
$('.js-add-risk').on('click', function(e){
  e.preventDefault();
  $('.risks-wrapper').append($('#append_source').html());
});
") ?>
