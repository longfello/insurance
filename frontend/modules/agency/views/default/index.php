<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="agency-default-index">
    <h1><?php echo 'this view Agency and Paqe or Profile by Agent'; ?></h1>

    <br>
    <a class="btn btn-primary"  href="/agency/sign-in/logout.html">Выход</a>
    <br>
    <a class="btn btn-primary"  href="<?=Url::to(['/agency/users'])?>">Менеджеры</a>
    <br>
    <?= \common\widgets\ChatAgency::widget() ?>

    <div class="col-lg-4">
        <?php $form = ActiveForm::begin(['id' => 'form-chat','enableClientValidation' => false,'options' => ['enctype' => 'multipart/form-data']]); ?>
        <?php echo $form->field($model, 'text')->textArea(['rows' => 5,'value'=>'']); ?>
        <?php echo $form->field($model, 'doc')->fileInput(); ?>
        <div class="form-group">
            <?php echo Html::submitButton(Yii::t('frontend', 'Отправить'), ['class' => 'btn btn-primary send']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
$this->registerJs(<<<JS

window.onload = function(){
				document.getElementById('scroll').scrollTop = 9999;
			}


    // $('.send').on('click',function(){
    //   
    //     var form = $('#form-chat').serialize();
    //     $.ajax({
    //       url: "/agency/default/index.html",
    //       dataType: "json", 
    //       type:"POST", 
    //       data: form,
    //       success: function( data ) {
    //           if((data.errors) && (data.errors.length != 0)){
    //             alert(data.errors.text);
    //           } else  {              
    //            console.log(data.mess);
    //            //location.reload();
    //           }           
    //       }, 
    //       error: function () {
    //         alert('Что-то пошло не так !');
    //       }
    //     });
    // });
 
JS
);
