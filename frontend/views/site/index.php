<?php
/* @var $this yii\web\View */
$this->title = Yii::$app->name;
xj\modernizr\ModernizrAsset::register($this);
?>
<div class="page__inner page__inner_index">
  <div data-scroll-speed="10" class="js-scroll-column page__left page__left_main">
    <?= $this->renderFile('@frontend/views/layouts/default-left.php') ?>
  </div>
  <div class="js-scroll-column page__right">
    <div class="insurance-find">


      <div class="insurance-find__type insurance-find__type_soon active" id="calc-tour">

        <div class="insurance-find__header js-local-hrefs">
            <a href="#" class="insurance-find__title insurance-find__title_white title title_size_l title_uppercase">Страхование путешественников</a>
            <div class="filter__desc">
                Туристическая страховка - это гарантия покрытия медицинских и медико-транспортных расходов во время путешествий и организация экстренной медицинской помощи. Во многих странах наличие такого полиса - обязательное условие при выдаче визы.
            </div>
            <div class="insurance-preview-image insurance-preview-image_travelers"></div>
        </div>

        <div class="insurance-find-form insurance-find-form_travelers">
            <?= \common\components\Calculator\Calculator::homePageForm(); ?>
        </div>
      </div>

      <!---- Страхование жизни ----->

      <div class="insurance-find__type insurance-find__type_soon" id="calc-life">

        <div class="insurance-find__header js-local-hrefs">
            <a href="#" class="insurance-find__title insurance-find__title_white title title_size_l title_uppercase">Страхование жизни</a>
            <div class="filter__desc">
                Страхование жизни от несчастного случая дает возможность получить денежную компенсацию при причинении вредаздоровью или потере кормильца
            </div>
            <div class="insurance-preview-image insurance-preview-image_life"></div>
        </div>

        <div class="insurance-find-form insurance-find-form_life">
            <form id="w2" class="insurance-find__body ajax-reloader form-vertical" action="" method="post">
                <div class="filter insurance-find__filter">
                  <div class="insurance-find__title insurance-find__title_grey title title_size_l">Страхование жизни</div>
                  <div class="filter__desc">
                      Страхование жизни от несчастного случая дает возможность получить денежную компенсацию при причинении вредаздоровью или потере кормильца
                  </div>
                    <div class="filter__country">
                        <div class="filter__label filter__label_black title title_size_s">Кого застраховать</div>
                        <div class="form-group">
                            <select class="js-select select">
                                <option>Взрослый</option>
                                <option>Взрослый</option>
                                <option>Взрослый</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter__insurance-sum">
                        <div class="filter__label filter__label_black title title_size_s">Страховая сумма</div>
                        <div class="form-group">
                            <input type="text" name="insurance-sum" class="input input_size_m form-control">
                        </div>
                    </div>
                </div>
                <div class="filter__submit filter__submit_life filter__submit_disabled">
                    <button type="submit" class="button button_color_green button_size_l button_uppercase">Скоро Вы сможете оформить полис на нашем сайте</button>
                </div>
            </form>
        </div>
        <div class="insurance-image insurance-image_life">
            <img src="/img/filter-life-insurance.png">
        </div>
      </div>

      <!------ Страхование жизни заёмщика ипотеки ------->

      <div class="insurance-find__type insurance-find__type_soon" id="calc-ipo">

        <div class="insurance-find__header js-local-hrefs">
          <a href="#" class="insurance-find__title insurance-find__title_ipo insurance-find__title_white title title_size_l title_uppercase">Страхование жизни заемщика ипотеки</a>
          <div class="filter__desc">
              Полис страхования жизни заёмщика ипотеки позволяет снизить процентную ставку по кредиту. При  страховом случае банку выплачивается вся сумма займа вместе с процентами, при этом предметы залога остаются в собственности владельца полиса.
          </div>
          <div class="insurance-preview-image insurance-preview-image_ipo"></div>
        </div>

        <div class="insurance-find-form insurance-find-form_ipo">
            <form id="w3" class="insurance-find__body ajax-reloader form-vertical" action="" method="post">
                <div class="filter insurance-find__filter">
                    <div class="insurance-find__title insurance-find__title_grey title title_size_l">Страхование жизни заемщика ипотеки</div>
                    <div class="filter__desc">
                        Полис страхования жизни заёмщика ипотеки позволяет снизить процентную ставку по кредиту. При  страховом случае банку выплачивается вся сумма займа вместе с процентами, при этом предметы залога остаются в собственности владельца полиса.
                    </div>
                    <div class="filter__arrears">
                        <div class="filter__label filter__label_black title title_size_s">Остаток задолженности</div>
                        <div class="form-group">
                            <input type="text" name="arrears" class="input input_size_m form-control">
                        </div>
                    </div>
                    <div class="filter__polis-begin">
                        <div class="filter__label filter__label_black title title_size_s">Начало действия полиса</div>
                        <div class="group-input">
                            <div class="date-field filter__input">
                                <div class="form-group required">
                                    <input type="text" id="polis-begin-date" class="input input_color_black input_size_m js-datepicker form-control" name="polis-begin" autocomplete="off" aria-required="true">
                                    <div class="help-block"></div>
                                </div>
                                <div class="date-field__icon">
                                    <svg class="icon icon_calendar ">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-calendar"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="filter__submit filter__submit_ipo filter__submit_disabled">
                    <button type="submit" class="button button_color_green button_size_l button_uppercase">Скоро Вы сможете оформить полис на нашем сайте</button>
                </div>
            </form>
        </div>
        <div class="insurance-image insurance-image_ipo">
          <img src="/img/filter-ipo-insurance.png">
        </div>
      </div>

      <!------ Страхование ипотеки ------->

      <div class="insurance-find__type insurance-find__type_soon" id="calc-zalog">

        <div class="insurance-find__header js-local-hrefs">
          <a href="#" class="insurance-find__title insurance-find__title_white title title_size_l title_uppercase">Страхование Ипотеки</a>
          <div class="filter__desc">
            Ипотечное страхование - обязательное условие при оформлении ипотеки. По закону заемщик обязан страховать только предмет залога от повреждения и полного уничтожения.
          </div>
          <div class="insurance-preview-image insurance-preview-image_zalog"></div>
        </div>

        <div class="insurance-find-form insurance-find-form_zalog">
          <form id="w4" class="insurance-find__body ajax-reloader form-vertical" action="" method="post">
            <div class="filter insurance-find__filter">
              <div class="insurance-find__title insurance-find__title_grey title title_size_l">Страхование Ипотеки</div>
              <div class="filter__desc">
                  Ипотечное страхование - обязательное условие при оформлении ипотеки. По закону заемщик обязан страховать только предмет залога от повреждения и полного уничтожения.
              </div>
              <div class="filter__zalog">
                  <div class="filter__label filter__label_black title title_size_s">Объект страхования</div>
                  <div class="form-group">
                      <select name="" class="js-select select">
                          <option>Квартира</option>
                          <option>Квартира</option>
                          <option>Квартира</option>
                          <option>Квартира</option>
                          <option>Квартира</option>
                      </select>
                  </div>
              </div>
              <div class="filter__insurance-sum">
                  <div class="filter__label filter__label_black title title_size_s">Страховая сумма</div>
                  <div class="form-group">
                      <input type="text" name="insurance-sum" class="input input_size_m form-control">
                  </div>
              </div>
            </div>
            <div class="filter__submit filter__submit_ipo filter__submit_disabled">
              <button type="submit" class="button button_color_green button_size_l button_uppercase">Скоро Вы сможете оформить полис на нашем сайте</button>
            </div>
          </form>
        </div>
        <div class="insurance-image insurance-image_zalog">
          <img src="/img/filter-zalog-insurance.png">
        </div>
      </div>

      <!----- Страхование имущества ------->

      <div class="insurance-find__type insurance-find__type_soon" id="calc-imu">

        <div class="insurance-find__header js-local-hrefs">
            <a href="#" class="insurance-find__title insurance-find__title_white title title_size_l title_uppercase">Страхование Имущества</a>
            <div class="filter__desc">
                Ипотечное страхование - обязательное условие при оформлении ипотеки. По закону заемщик обязан страховать только предмет залога от повреждения и полного уничтожения.
            </div>
            <div class="insurance-preview-image insurance-preview-image_imu"></div>
        </div>

        <div class="insurance-find-form insurance-find-form_imu">
            <form id="w5" class="insurance-find__body ajax-reloader form-vertical" action="" method="post">
                <div class="filter insurance-find__filter">
                    <div class="insurance-find__title insurance-find__title_grey title title_size_l">Страхование Имущества</div>
                    <div class="filter__desc">
                      Ипотечное страхование - обязательное условие при оформлении ипотеки. По закону заемщик обязан страховать только предмет залога от повреждения и полного уничтожения.
                    </div>
                    <div class="filter__imu">
                        <div class="filter__label filter__label_black title title_size_s">Объект страхования</div>
                        <div class="form-group">
                            <select name="" class="js-select select">
                                <option>Квартира</option>
                                <option>Квартира</option>
                                <option>Квартира</option>
                                <option>Квартира</option>
                                <option>Квартира</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter__insurance-sum">
                        <div class="filter__label filter__label_black title title_size_s">Страховая сумма</div>
                        <div class="form-group">
                            <input type="text" name="insurance-sum" class="input input_size_m form-control">
                        </div>
                    </div>
                    <div class="filter__polis-begin">
                        <div class="filter__label filter__label_black title title_size_s">Начало действия полиса</div>
                        <div class="group-input">
                            <div class="date-field filter__input">
                                <div class="form-group required">
                                    <input type="text" id="polis-begin-date" class="input input_color_black input_size_m js-datepicker form-control" name="polis-begin" autocomplete="off" aria-required="true">
                                    <div class="help-block"></div>
                                </div>
                                <div class="date-field__icon">
                                    <svg class="icon icon_calendar ">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-calendar"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="filter__submit filter__submit_ipo filter__submit_disabled">
                    <button type="submit" class="button button_color_green button_size_l button_uppercase">Скоро Вы сможете оформить полис на нашем сайте</button>
                </div>
            </form>
        </div>
        <div class="insurance-image insurance-image_imu">
            <img src="/img/filter-imu-insurance.png">
        </div>
      </div>

      <!------ Инвестиционное страхование жизни ------->

      <div class="insurance-find__type insurance-find__type_soon" id="calc-invest-life">

          <div class="insurance-find__header js-local-hrefs">
              <a href="#" class="insurance-find__title insurance-find__title_white title title_size_l title_uppercase">Инвестиционное страхование жизни</a>
              <div class="filter__desc">
                  Инвестиционное страхование жизни - это финансовый инструмент, сочетающий участие в фондовом рынке, защиту инвестированного капитала и страховую защиту при непредвиденных обстоятельствах.
              </div>
              <div class="insurance-preview-image insurance-preview-image_invest-life"></div>
          </div>

          <div class="insurance-find-form insurance-find-form_invest-life">
              <form id="w6" class="insurance-find__body ajax-reloader form-vertical" action="" method="post">
                  <div class="filter insurance-find__filter">
                      <div class="insurance-find__title insurance-find__title_grey title title_size_l">Инвестиционное страхование жизни</div>
                      <div class="filter__desc">
                          Инвестиционное страхование жизни - это финансовый инструмент, сочетающий участие в фондовом рынке, защиту инвестированного капитала и страховую защиту при непредвиденных обстоятельствах.
                      </div>
                      <div class="filter__invest-life">
                          <div class="filter__label filter__label_black title title_size_s">Сколько вложить?</div>
                          <div class="form-group">
                              <input type="text" name="insurance-sum" class="input input_size_m form-control">
                          </div>
                      </div>
                      <div class="filter__period">
                          <div class="filter__label filter__label_black title title_size_s">За какой период?</div>
                          <div class="period__progress">
                              <div class="progress__range">
                                  <input name="period" value="" class="js-progress progress__input" data-variants="1 год,3 года,10 лет" readonly="">
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="filter__submit filter__submit_invest-life filter__submit_disabled">
                      <button type="submit" class="button button_color_green button_size_l button_uppercase">Скоро Вы сможете оформить полис на нашем сайте</button>
                  </div>
              </form>
          </div>
          <div class="insurance-image insurance-image_invest-life">
              <img src="/img/filter-invest-life-insurance.png">
          </div>

      </div>

      <!----- Накопительное страхование жизни ------>

      <div class="insurance-find__type insurance-find__type_soon" id="calc-nakop-life">
          
          <div class="insurance-find__header js-local-hrefs">
              <a href="#" class="insurance-find__title insurance-find__title_white title title_size_l title_uppercase">Накопительное страхование жизни</a>
              <div class="filter__desc">
                  Сочетание страхования жизни и здоровья человека c программой накопления, сохранения и увеличения вашего капитала.
              </div>
              <div class="insurance-preview-image insurance-preview-image_nakop-life"></div>
          </div>

        <div class="insurance-find-form insurance-find-form_nakop-life">
            <form id="w7" class="insurance-find__body ajax-reloader form-vertical" action="" method="post">
                <div class="filter insurance-find__filter">
                    <div class="insurance-find__title insurance-find__title_grey title title_size_l">Накопительное страхование жизни</div>
                    <div class="filter__desc">
                        Сочетание страхования жизни и здоровья человека c программой накопления, сохранения и увеличения вашего капитала.
                    </div>
                    <div class="filter__invest-life">
                        <div class="filter__label filter__label_black title title_size_s">Сколько вложить?</div>
                        <div class="form-group">
                            <input type="text" name="insurance-sum" class="input input_size_m form-control">
                        </div>
                    </div>
                    <div class="filter__period">
                        <div class="filter__label filter__label_black title title_size_s">За какой период?</div>
                        <div class="period__progress">
                            <div class="progress__range">
                                <input name="period" value="" class="js-progress progress__input" data-variants="1 год,3 года,10 лет" readonly="">
                            </div>
                        </div>
                    </div>
                </div>
              <div class="filter__submit filter__submit_invest-life filter__submit_disabled">
                <button type="submit" class="button button_color_green button_size_l button_uppercase">Скоро Вы сможете оформить полис на нашем сайте</button>
              </div>
            </form>
        </div>
        <div class="insurance-image insurance-image_nakop-life">
            <img src="/img/filter-nakop-insurance.png">
        </div>
      </div>

    </div>
  </div>
</div>
