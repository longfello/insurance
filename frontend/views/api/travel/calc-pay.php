<?php
  /**
   * @var $model \common\models\ProgramResult
   * @var $order \common\models\Orders
   */

  $payu = Yii::$app->payu; // new \common\components\PayU('', 'merchant_name', 'secret_key');

  $formData = $payu->initLiveUpdateFormData(array(
    // Данные заказа
    'ORDER_DATE' => date('Y-m-d H:i:s'),
    'ORDER_PNAME[]' => 'Insurance police',
    'ORDER_PCODE[]' => '#'.$order->id,
	  'ORDER_PINFO[]' => \yii\helpers\BaseInflector::transliterate($order->api->name),
    'ORDER_PRICE[]' => $order->price,
    'ORDER_QTY[]' => 1,
    'ORDER_VAT[]' => 0,
    'ORDER_REF'   => $order->id,
    'PRICES_CURRENCY' => $order->currency->char_code,
    'LANGUAGE' => 'RU',

    'TESTORDER' => (Yii::$app->payu->debug == 'true')?'TRUE':'FALSE',
    'AUTOMODE' => 1,
    // Данные плательщика
    'BILL_FNAME' => $model->calc->payer->first_name,
    'BILL_LNAME' => $model->calc->payer->last_name,
    'BILL_EMAIL' => $model->calc->payer->email,
    'BILL_PHONE' => $model->calc->payer->phone,
    // Данные получателя
    'DELIVERY_FNAME' => $model->calc->payer->first_name,
    'DELIVERY_LNAME' => $model->calc->payer->last_name,
    'DELIVERY_PHONE' => $model->calc->payer->phone,
  ), \yii\helpers\Url::to('/api/'.\common\components\Calculator\forms\prototype::SLUG_TRAVEL.'/calc-payment-done.html?order='.$order->id, true), 'PAY_BY_CLICK');


function makeString ($name, $val)
{
	if (!is_array($val))
		echo '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars($val).'">'."\n";
	else
		foreach ($val as $v) makeString($name, $v);
}



?>
<form action="<?php echo \common\components\PayU::LU_URL; ?>" method="post">
	<?php
	foreach ($formData as $formDataKey => $formDataValue)
		makeString($formDataKey, $formDataValue);
	?>

  <div class="page__left page__left_travelers-data">
    <div class="payment-left-wrap">
      <div class="success-left">
        <svg class="icon document-check-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 60 60">
          <g>
            <path d="M45.5,34c-7.168,0-13,5.832-13,13s5.832,13,13,13s13-5.832,13-13S52.668,34,45.5,34z M45.5,58c-6.065,0-11-4.935-11-11   s4.935-11,11-11s11,4.935,11,11S51.565,58,45.5,58z" fill="#77bc1f"/>
            <path d="M50.679,41.429l-5.596,8.04l-3.949-3.242c-0.426-0.351-1.057-0.288-1.407,0.139c-0.351,0.427-0.289,1.057,0.139,1.407   l4.786,3.929c0.18,0.147,0.404,0.227,0.634,0.227c0.045,0,0.091-0.003,0.137-0.009c0.276-0.039,0.524-0.19,0.684-0.419l6.214-8.929   c0.315-0.453,0.204-1.077-0.25-1.392C51.617,40.863,50.995,40.976,50.679,41.429z" fill="#77bc1f"/>
            <path d="M29.551,48H12.5c-0.552,0-1-0.447-1-1s0.448-1,1-1h17.051c0.133-2.142,0.687-4.167,1.584-6H12.5c-0.552,0-1-0.447-1-1   s0.448-1,1-1h19.782c2.884-4.222,7.732-7,13.218-7c1.026,0,2.027,0.106,3,0.292V14.586L33.914,0H1.5v60h34.708   C32.41,57.278,29.859,52.943,29.551,48z M34.5,4l10,10h-10V4z M12.5,14h10c0.552,0,1,0.447,1,1s-0.448,1-1,1h-10   c-0.552,0-1-0.447-1-1S11.948,14,12.5,14z M12.5,22h25c0.552,0,1,0.447,1,1s-0.448,1-1,1h-25c-0.552,0-1-0.447-1-1   S11.948,22,12.5,22z M12.5,30h25c0.552,0,1,0.447,1,1s-0.448,1-1,1h-25c-0.552,0-1-0.447-1-1S11.948,30,12.5,30z" fill="#77bc1f"/>
          </g>
        </svg>
      </div>
    </div>
  </div>

  <div data-scroll-speed="10" class="page__right page__right_travelers-data">
    <div class="page__right-inner page__right-inner_travelers-data">
      <div class="page__tabs tabs tabs_rules">
          <div class="polis-cost-wrap">
              <div class="polis-cost-inner">
                  <div class="polis-total-cost">
                      <div class="polis-total-cost__title">Стоимость полиса</div>
                      <div class="polis-total-cost__sum">
                          <span class="polis-total-cost__val"><?= \common\models\Currency::convert($order->price, $order->currency->char_code, \common\models\Currency::RUR, 2) ?></span>
                          <span>
                              <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="27" height="27" x="0px" y="3px" viewBox="0 0 330 330">
                              <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
                c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
                c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
                c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"></path>
                            </svg>
                          </span>
                      </div>
                  </div>
                  <div class="travelers-full-data">
                      <div class="travelers-full-data__annotation">Пожалуйста, проверьте, правильно ли указаны данные:</div>
                      <div class="travelers-full-data__data-block">
                          <div class="travelers-full-data__travel-dates">
                              <div class="travelers-full-data__title">Даты поездки:</div>
                              <div class="travelers-full-data__dates-values">с <?= $model->calc->dateFrom ?> по <?= $model->calc->dateTo ?></div>
                          </div>
                          <div class="travelers-full-data__country">
                              <div class="travelers-full-data__title">Страны:</div>
                              <div class="travelers-full-data__country-value">
                                <?php foreach($model->calc->countriesModels as $country){ ?>
                                    <p><?= $country->name ?></p>
                                <?php } ?>
                              </div>
                          </div>
                          <div class="travelers-full-data__travelers">
                              <div class="travelers-full-data__title">Застрахованные:</div>
                              <ul class="travelers-full-data__list">
                                  <?php foreach ($model->calc->travellers as $traveller){ ?>
                                    <li class="travelers-full-data__traveler">
                                        <div class="travelers-full-data__name"><?= $traveller->first_name ?> <?= $traveller->last_name ?></div>
                                        <div class="travelers-full-data__birth"><?= $traveller->birthdayAsDate() ?></div>
                                    </li>
                                  <?php } ?>
                              </ul>
                          </div>
                      </div>
                      <button type="submit" class="travelers-full-data__send">Перейти к оплате</button>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>

</form>
