<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<h2>Чат (frontend)</h2>

<div class ='chat-window' id="scroll" style = 'height:200px;width:500px;overflow-y:scroll;'>
    <?php foreach ($items as $item){ ?>
        <span>Сотрудник: <?=$item->from_user_id?> - Агенство: <?=$item->id_agency?></span><br>
        <span>Текст: <?=$item->message?></span><br>
        <?php if(!empty($item->file)) { ?>
            <a href = '<?= Url::toRoute(['/agency/default/download-file', 'id' => $item->id]); ?>'>-Скачать</a>
            -Файл: <?=$item->file ?>
        <?php } ?>
        <br>
    <?php } ?>

</div>


