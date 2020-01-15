<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 10.03.17
 * Time: 16:11
 *
 * @var $model \common\models\Api
 */

$this->title = 'Страховая компания '.$model->name;

?>
<main class="main main_companies">
	<div class="insurance-companies-title">Страховые компании</div>
	<div class="companies-slider">
		<div class="companies-slider__wrapper owl-carousel">
			<?php
			$curr=0;
			foreach(\common\models\Api::find()->where(['enabled' => 1])->orderBy(['name' => SORT_ASC])->all() as $apiModel){ $curr++; /** @var $apiModel \common\models\Api */ ?>
				<a href="/company/<?=$apiModel->id?>.html" class="companies-slider__slide">
					<img src="<?= $apiModel->thumbnail_base_url.'/'.$apiModel->thumbnail_path ?>" class="companies-slider__company-logo">
					<?php if ($curr%2==0) {?>
						<svg version="1.1" id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
							 viewBox="0 0 176 184" style="enable-background:new 0 0 176 184;" xml:space="preserve">
							<g>
								<polygon class="st0" points="0,169 176,184 176,0 0,15 	"/>
							</g>
						</svg>
					<?php } else { ?>
						<svg version="1.1" id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
							 viewBox="0 0 176 184" style="enable-background:new 0 0 176 184;" xml:space="preserve">
							<g>
								<polygon class="st0" points="176,169 0,184 0,0 176,15 	"/>
							</g>
						</svg>
					<?php } ?>
				</a>
			<?php } ?>
		</div>
	</div>
	<div class="company-wrapper">
		<h1 class="company-title">Страховая компания <?= $model->name; ?></h1>
		<div class="company-card">
			<div class="company-card__description">
				<?= $model->description ?>
			</div>
			<div class="company-card__passport">
				<div class="company-card__asn-raiting">
					<div class="company-card__raiting-title">Рейтинг АСН</div>
					<div class="company-card__raiting-val"><?= $model->rate_asn ?></div>
				</div>
				<div class="company-card__ra-raiting">
					<div class="company-card__raiting-title">Рейтинг Эксперта РА</div>
					<div class="company-card__raiting-val"><?= $model->rate_expert ?></div>
				</div>
			</div>
		</div>
		<div class="company-files owl-carousel">
			<?php foreach($model->files as $file){
				$path_parts = pathinfo($file->file_base_url.'/'.$file->file_path);
				?>
			<div class="company-files__item">
				<a href="<?= $file->file_base_url.'/'.$file->file_path ?>" class="company-files__file-type"><?= mb_strtoupper($path_parts['extension']); ?></a>
				<a href="<?= $file->file_base_url.'/'.$file->file_path ?>" class="company-files__file-name"><?= $file->name ?></a>
			</div>
			<?php } ?>
		</div>
		<div class="company-contacts">
			<div class="company-contacts__service">
				<a href="" class="company-contacts__service-link">Ссылка на сервисный центр</a>
				<div class="company-contacts__service-name"><?= $model->service_center_url ?></div>
			</div>
			<div class="company-contacts__contacts">
				<?php foreach($model->phones as $phone) {?>
					<div class="contacts-item">
						<div class="contacts-item__title"><?= trim($phone->name) ?></div>
						<a href="tel:<?= trim($phone->phone) ?>" class="contacts-item__phone"><?= trim($phone->phone) ?></a>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</main>