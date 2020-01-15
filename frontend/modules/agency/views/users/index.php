<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<style>
    .glyphicon{
        cursor: pointer;
    }
</style>
<h1>Page Managers</h1>
<div class="row">
    <div class="col-lg-12">
        <h2>Данные агентства</h2>
         Название   <?=$data_admin_agency['name'];?><br>
         ID-agency текущего агенства  <?=$data_admin_agency['id'];?><br>
        ID-admin-agency  текущего администратора  <?=$cur_admin_agency_id;?><br>
    </div>
    <div class="col-lg-12">
        <h2>Менеджеры агентства</h2>
        <div class="col-lg-12">
            <a class="btn btn-success" href="<?=Url::to(['/agency/users/create', 'agency_id' => $data_admin_agency['id']])?>">Добавить нового</a>
        </div>
        <?php foreach ($managers as $manager) { ?>
            <div class="col-lg-12">
                <div class="col-lg-3">id=<?=$manager['id']?> - <?=$manager['username']?></div>
                <div class="col-lg-3">
                    <a href="<?=Url::to(['/agency/users/update', 'user_id' => $manager['id']])?>"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="<?=Url::to(['/agency/users/delete', 'user_id' => $manager['id']])?>"><span class="glyphicon glyphicon-trash del-proj" data-id="<?=$manager['id']?>"></span></a>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12">
         <a class="btn btn-primary"  href="/agency/sign-in/logout.html">Выход</a>
    </div>
</div>
