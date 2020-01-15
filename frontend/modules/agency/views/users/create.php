<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\User;
?>

<h1>Create new Manager</h1>
<div class="row">
    <div class="col-lg-5">
        <?php $form = ActiveForm::begin(['id' => 'form-signup','enableClientValidation' => false,]); ?>
        <?php echo $form->field($model, 'username') ?>
        <?php echo $form->field($model, 'fio') ?>
        <?php echo $form->field($model, 'email') ?>
        <?php echo $form->field($model, 'city') ?>
        <?php echo $form->field($model, 'phone') ?>
        <?php echo $form->field($model, 'password')->passwordInput() ?>
        <?php echo $form->field($model, 'password_repeat')->passwordInput() ?>
        <?php echo $form->field($model, 'status')->dropDownList([
            '' => 'Не выбран',
            User::STATUS_NOT_ACTIVE => 'Заблокированый',
            User::STATUS_ACTIVE => 'Активный',
        ],['class'=>'form-control']);
        ?>
        <?php echo $form->field($model, 'role')->dropDownList([
            '' => 'Не выбран',
            User::ROLE_ADMINISTRATOR_AGENCY => 'Администратор агентства',
            User::ROLE_MANAGER_AGENCY => 'Менеджер агентства',
        ],['class'=>'form-control']);
        ?>

        <div class="form-group">
            <?php echo Html::submitButton(Yii::t('frontend', 'Добавить'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
