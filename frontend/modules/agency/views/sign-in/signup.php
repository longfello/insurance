<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\TravelNetworks;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\SignupForm */

$this->title = Yii::t('frontend', 'Signup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">

    <h1>Page Register</h1>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup','enableClientValidation' => false,]); ?>
            <?php echo $form->field($model, 'username') ?>
            <?php echo $form->field($model, 'email') ?>
            <?php echo $form->field($model, 'password')->passwordInput() ?>
            <?php echo $form->field($model, 'chief_name') ?>
            <?php echo $form->field($model, 'chief_position') ?>
            <?php echo $form->field($model, 'name') ?>
            <?php echo $form->field($model, 'legal_region') ?>
            <?php echo $form->field($model, 'legal_city') ?>
            <?php echo $form->field($model, 'legal_address') ?>
            <?php echo $form->field($model, 'legal_index') ?>
            <?php echo $form->field($model, 'actual_region') ?>
            <?php echo $form->field($model, 'actual_city') ?>
            <?php echo $form->field($model, 'actual_address') ?>
            <?php echo $form->field($model, 'actual_index') ?>
            <?php echo $form->field($model, 'phone') ?>
            <?php echo $form->field($model, 'inn') ?>
            <div class = "option-kpp">
             <?php echo $form->field($model, 'kpp')->textInput(['class' => 'kpp form-control']) ?>
            </div>
            <?php echo $form->field($model, 'ogrn') ?>
            <?php echo $form->field($model, 'okved') ?>
            <?php echo $form->field($model, 'okpo') ?>
            <?php echo $form->field($model, 'okato') ?>
            <?php echo $form->field($model, 'checking_account') ?>
            <?php echo $form->field($model, 'bank') ?>
            <?php echo $form->field($model, 'correspondent_account') ?>
            <?php echo $form->field($model, 'bik') ?>
            <?php echo $form->field($model, 'href') ?>
            <?php echo $form->field($model, 'comment')->textArea(['rows' => 5]) ?>
            <?php  // список сети туристических агентств
                   $travel_networks = TravelNetworks::find()->all();
                   $items = ArrayHelper::map($travel_networks,'id','name');
                   $params = ['prompt' => 'Не выбран', 'class'=>'form-control'];
            ?>
            <?php echo $form->field($model, 'travel_network_id')->dropDownList($items,$params); ?>
            <?php echo $form->field($model, 'company_type')->dropDownList([
                                '' => 'Не выбран',
                                'ooo' => 'OOO',
                                'zao' => 'ЗAO',
                                'ip' => 'ИП',
                            ],['class'=>'form-control option-company-type']);
             ?>
            <?php echo $form->field($model, 'company_tax_type')->dropDownList([
                                  '' => 'Не выбран',
                                  'easy' => 'Упрощённая',
                                  'common' => 'Обычная',
                            ],['class'=>'form-control']);
             ?>
             <?php echo $form->field($model, 'cooperation_form')->dropDownList([
                                  '' => 'Не выбран',
                                  'contract' => 'Агентский договор',
                                  'iframe' => 'Iframe',
                                  'api' => 'API',
                          ],['class'=>'form-control']);
             ?>
            <?php echo $form->field($model, 'value_scenario')->label(false)->hiddenInput(['value' => 1,'class'=>'scenario']); ?>
            <div class="form-group">
                <?php echo Html::submitButton(Yii::t('frontend', 'Signup'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS

    $('.option-company-type').on('change', function(){
            var oct = $('.option-company-type').val();
            if(oct === 'ip'){
              $('.scenario').val(2);
              $('.kpp').val('');
              $('.option-kpp').css({'display':'none'});
            } else {
              $('.scenario').val(1);
              $('.option-kpp').css({'display':'block'});
            }
    });
 
JS
);