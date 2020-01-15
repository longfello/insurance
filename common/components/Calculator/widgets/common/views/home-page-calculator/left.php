<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 11.08.17
 * Time: 15:21
 *
 * @var $this \yii\web\View
 * @var $page \common\models\Page
 * @var $type \common\models\InsuranceType
 * @var $widget string
 * @var $availableTypes \common\models\InsuranceType[]
 *
 */

?>
<ul class="insurance-types__row">
	<?php foreach($availableTypes as $one) {
		$url = $one->calcPage ? $one->calcPage->createUrl() : false;
		if ($url || !$one->active) {
			?>
			<li class="insurance-types__item <?= ($type && $type->slug == $one->slug) ? "insurance-types__item_current" : "" ?> <?= $url ? "" : "insurance-types__item_disabled" ?> <?= $one->active?"active":"passive" ?> ">
				<a class="insurance-types__wrap" href="<?= $url ? $url : '#' ?>">
					<div class="insurance-types__icon">
						<svg class="icon icon_<?= $one->slug ?> ">
							<use xlink:href="#m-<?= $one->slug ?>"/>
						</svg>
					</div>
					<div class="insurance-types__link link link_color_black link_line_solid">
						<?= $one->name ?>
					</div>
					<div class="insurance-types__green-block">
						<span><?= $one->name ?></span>
					</div>
				</a>
			</li>
		<?php }
	}?>
</ul>


