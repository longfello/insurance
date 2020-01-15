<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\User;
use Yii;
?>

<h1>Update Manager</h1>

<div class="row">
    <div class="col-lg-5">
        <?php $form = ActiveForm::begin(['id' => 'form-signup','enableClientValidation' => false,]); ?>
        <?php echo $form->field($model, 'username')->textInput(['value' => !empty($model->username) ? $model->username : $user->username]) ?>
        <?php echo $form->field($model, 'fio')->textInput(['value' => !empty($model->fio) ? $model->fio : $user->userProfile->firstname]) ?>
        <?php echo $form->field($model, 'email')->textInput(['value' => !empty($model->email) ? $model->email : $user->email]) ?>
        <?php echo $form->field($model, 'city')->textInput(['value' => !empty($model->city) ? $model->city : $user->userProfile->city]) ?>
        <?php echo $form->field($model, 'phone')->textInput(['value' => !empty($model->phone) ? $model->phone : $user->userProfile->phone]) ?>
        <?php echo $form->field($model, 'password')->passwordInput() ?>
        <?php echo $form->field($model, 'password_repeat')->passwordInput() ?>
        <?php echo $form->field($model, 'status')->dropDownList([
            '' => 'Не выбран',
            User::STATUS_NOT_ACTIVE => 'Заблокированый',
            User::STATUS_ACTIVE => 'Активный',
        ],['class'=>'form-control','value' => !empty($model->status) ? $model->status : $user->status]);
        ?>
        <?php echo $form->field($model, 'role')->dropDownList([
            '' => 'Не выбран',
            User::ROLE_ADMINISTRATOR_AGENCY => 'Администратор агентства',
            User::ROLE_MANAGER_AGENCY => 'Менеджер агентства',
        ],['class'=>'form-control',
            'value' => !empty($model->role) ? $model->role : $user->getRoleUserAgency($user->id)['item_name']]);
        ?>

        <div class="form-group">
            <?php echo Html::submitButton(Yii::t('frontend', 'Обновить'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
