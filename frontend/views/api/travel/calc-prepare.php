<?php
  /** @var $items \common\models\ProgramResult[] */
  /** @var $form \common\components\Calculator\forms\TravelForm */
?>
<div class="company-list__title title title_size_xl">Страхование путешественников</div>

<div class="selected-data">
  <div class="selected-country"><?= $form->countriesAsString() ?></div>
  <div class="selected-dates">
    <div class="selected-dates__from"><?= Yii::$app->formatter->asDate(\DateTime::createFromFormat('d.m.Y', trim($form->dateFrom)), 'd MMM') ?></div>
    <span class="selected-dates__separ">—</span>
    <div class="selected-dates__to"><?= Yii::$app->formatter->asDate(\DateTime::createFromFormat('d.m.Y', trim($form->dateTo)), 'd MMM') ?></div>
  </div>
</div>


<div class="insurance-sum">
  <div class="additional-options__label">Страховая сумма</div>
  <div class="insurance-sum__form">
	  <?php

    $newForm = $new_object = unserialize(serialize($form));
    /** @var $newForm \common\components\Calculator\forms\TravelForm */

	  $models = \common\models\CostInterval::find()->orderBy(['from' => SORT_ASC])->all();
	  $i=0;
	  foreach($models as $one){
          $cost_from = ($one->from>0)?$one->from-1:$one->from;

		  /** @var $one \common\models\CostInterval */

	    // TODO: Change cost interval
	    $newForm->changeParamVariant('common\modules\filter\components\FilterParamSum', $one);
		  $filter = new \common\components\Calculator\filters\Filter(['form' => $newForm]);
      $minCost = $filter->getMinCost();
      $active = '';
	    $param = isset($form->params[1])?$form->params[1]:false;
	    if ($param && $param->handler){
	      $active = '';
	      if ($param && $param->handler && $param->handler->variant){
	        $active = ($param->handler->variant->id == $one->id)?"active":"";
        }
      }
		  ?>
        <a class="insurance-sum__item js-set-insurance-sum <?= $active ?>" href="#" data-id="<?= $one->id ?>" data-name="<?= $one->name ?>" data-no="<?= $i ?>">
          <input type="checkbox" name="minimal">
          <label class="insurance-sum__label insurance-sum__label_min">
            <div><?= $one->name ?></div>
            <div><?= $cost_from ?> — <?= $one->to ?></div>
            <div>от <?= $minCost ?></div>
          </label>
        </a>
		  <?php
      $i++;
	  }
	  ?>
  </div>
</div>

<div class="current-filters">
    <div class="current-filters__title">Выбрать параметры страховки</div>
    <div class="current-filters__num">Применён 1 фильтр из 30</div>

    <div class="filters-button-m">
      <svg class="icon icon_settings" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 489.7 489.7" width="32px" height="32px">
        <g>
          <g>
            <path d="M60.6,461.95c0,6.8,5.5,12.3,12.3,12.3s12.3-5.5,12.3-12.3v-301.6c34.4-5.9,60.8-35.8,60.8-71.9c0-40.3-32.8-73-73-73    s-73,32.7-73,73c0,36.1,26.3,66,60.8,71.9v301.6H60.6z M24.3,88.45c0-26.7,21.8-48.5,48.5-48.5s48.5,21.8,48.5,48.5    s-21.8,48.5-48.5,48.5S24.3,115.25,24.3,88.45z" fill="#FFFFFF"/>
            <path d="M317.1,401.25c0-36.1-26.3-66-60.8-71.9V27.75c0-6.8-5.5-12.3-12.3-12.3s-12.3,5.5-12.3,12.3v301.6    c-34.4,5.9-60.8,35.8-60.8,71.9c0,40.3,32.8,73,73,73S317.1,441.45,317.1,401.25z M195.6,401.25c0-26.7,21.8-48.5,48.5-48.5    s48.5,21.8,48.5,48.5s-21.8,48.5-48.5,48.5S195.6,427.95,195.6,401.25z" fill="#FFFFFF"/>
            <path d="M416.6,474.25c6.8,0,12.3-5.5,12.3-12.3v-301.6c34.4-5.9,60.8-35.8,60.8-71.9c0-40.3-32.8-73-73-73s-73,32.7-73,73    c0,36.1,26.3,66,60.8,71.9v301.6C404.3,468.75,409.8,474.25,416.6,474.25z M368.1,88.45c0-26.7,21.8-48.5,48.5-48.5    s48.5,21.8,48.5,48.5s-21.8,48.5-48.5,48.5C389.8,136.95,368.1,115.25,368.1,88.45z" fill="#FFFFFF"/>
          </g>
        </g>
      </svg>
    </div>
</div>

<div class="company-list__title title title_size_l company-list__search_size_l">Результаты поиска:</div>

<div class="company-list__item_wrapper2">

<?php if ($items){ ?>
    <?php foreach($items as $item){ ?>
      <div class="company-list__item <?= ($item->cost==0)?'program_not_calculated':''; ?>" data-api="<?= $item->api_id; ?>">
        <div class="company js-company">
          <div class="company__header">
              <div class="company__logo">
                  <div class="company__logo-thumb">
                      <img src="<?= $item->thumbnail_url ?>">
                  </div>
              </div>
              <div class="company__info company__info_m">
                  <div class="company__info-rating"><?= $item->rate_expert ?></div>
                  <div class="company__info-rating"><?= $item->rate_asn ?></div>
              </div>
              <div class="company__right-block">
                  <div class="company__price">
                      <span class="price-value">
                          <?= $item->cost ?>
                      </span>
                      <span class="ruble-icon">
                          <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="21" x="0px" y="0px" viewBox="0 0 330 330">
                            <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
              c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
              c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
              c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"/>
                          </svg>
                      </span>
                  </div>
                  <div class="company__buy">
                    <?= \common\components\Calculator\widgets\travel\BuyButtonWidget::widget(['program' => $item]) ?>
                  </div>
              </div>
          </div>
          <div class="company__info">
            <div class="company__info-more js-company-more-btn">
              <div class="company__info-more-link link link_color_green">
                Подробнее<i class="icon icon_pointer-down-green"></i>
              </div>
            </div>
            <div class="company__info-expert">
              <div class="company__info-title">Рейтинг "Эксперт РА"
              </div>
              <div class="company__info-rating"><?= $item->rate_expert ?></div>
            </div>
            <div class="company__info-asn">
              <div class="company__info-title">Рейтинг АСН
              </div>
              <div class="company__info-rating"><?= $item->rate_asn ?></div>
            </div>
          </div>
          <div class="company__content js-company-content">
            <div class="company__pdf-list">
              <div class="company__pdf-item">
                <div class="pdf">
                  <div class="pdf__icon">PDF</div>
                  <a href="<?= $item->rule_url ?>" target="_blank" class="link link_color_green pdf__link">Правила страхования</a>
                </div>
              </div>
              <div class="company__pdf-item company__pdf-item_sep">
                <div class="pdf">
                  <div class="pdf__icon">PDF</div>
                  <a href="<?= $item->police_url ?>" target="_blank" class="link link_color_green pdf__link">Образец полиса</a>
                </div>
              </div>
            </div>
            <div class="company__included">
              <div class="company__included-title title title_size_m">Что входит в покрытие</div>
              <table class="company__included-list">
                <?php foreach($item->risks as $name => $amount) { ?>
                  <?php if (!empty($name)) {?>
                      <tr class="company__included-row">
                        <td class="company__included-name"><?= $name ?></td>
                        <td class="company__included-price"><?= ($amount>0)?$amount."  &euro;":""; ?></td>
                      </tr>
                  <?php } ?>
                <?php } ?>
              </table>
            </div>
            <div class="company__footer">
              <div class="company__footer-buy">
	              <?= \common\components\Calculator\widgets\travel\BuyButtonWidget::widget(['program' => $item]) ?>
              </div>
              <div class="company__footer-price"><span class="price-value-footer"><?= $item->cost ?></span> &#x20bd;</div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
<?php } else { ?>
  <div class="company-list__item">
    <div class="company js-company">
      <div class="company__header">
        <div class="company__price">
          Нет результатов, удовлетворяющих выбранным параметрам
        </div>
      </div>
    </div>
  </div>
<?php } ?>

</div>


<script type="text/javascript">
  var price_request = [];
  $(document).ready(function(){
      $('a.js-set-insurance-sum').off('click').on('click', function(e){
          e.preventDefault();
          var value = $(this).data('no');
          $(".additional-options__progress_sum").find(".js-progress").each(function(){
              var slider = $(this).data("ionRangeSlider");
              slider.update({
                  from: value
              });
          });
      });

      $('.filters-button-m').on('click', function() {
          $('.page__right').addClass('page__right_roll');
          $('body').css({'overflow':'hidden'});
          $('.fade-out').show();
      });

      $('.fade-out').on("click", function () {
          $('.page__right').removeClass('page__right_roll');
          $('body').css({'overflow': 'auto'});
          $('.fade-out').fadeOut();
      });

      $('.program_not_calculated').each(function(){
         var program = $(this).find('input[name=program]').val();
         var api = $(this).data('api');
         var form = this;
          price_request[api] = $.ajax({
              type: "POST",
              url: '/api/travel/calc-api-cost.html',
              data: {'program':program},
              success: function(response){
                  delete price_request[api];
                  if (response.price){
                      $(form).removeClass('program_not_calculated');
                      $(form).find('.company__price .price-value, .company__footer-price .price-value-footer').html(response.price);
                      $(form).find('input[name=program]').val(response.program)
                      $(form).find('.company').removeClass('_open');
                  }
              },
              dataType: 'json'
          });
      });

      /*----- Количество применённых фильтров ------*/

      //$(".checkbox__input:checked")

  });
</script>