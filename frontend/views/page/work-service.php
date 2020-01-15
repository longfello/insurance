<?php
/**
 * @title Как это работает
 * @var $this \yii\web\View
 * @var $model \common\models\Page
 */
$this->title = $model->title;
?>
<div class="content page__inner page__inner_index">
  <div data-scroll-speed="10" class="js-scroll-column page__left page__left_static" style="z-index: 2; position: absolute; top: 0px;">

    <div class="how-work-ins-travel active">
      <div class="menu-how-work top">
        <div class="ontent-how-work__title title_size_xl">Как это работает?</div>
        <div class="menu-how-work__list">
	        <?= \common\components\Calculator\widgets\common\InsuranceTypeListWidget::widget(['layout' => 'how-it-work']); ?>
        </div>
        <span class="wrap-fa"><i class="fa fa-times" aria-hidden="true"></i></span>
      </div>
    </div>

    <footer class="footer page__footer">
      <div class="footer__social">
        <div class="footer__logo">
          <div class="footer__logo-icon">
            <svg class="icon icon_logo ">
              <use xlink:href="#icon-logo"/>
            </svg>
          </div>
          <div class="footer__logo-text">&copy; 2016 <br> BulloSafe
          </div>
        </div>
        <div class="footer__social-list socials">
          <div class="socials__item"><a href="https://www.facebook.com/bullosafe" target="_blank" class="link socials__link">
              <svg class="icon icon_fb ">
                <use xlink:href="#icon-fb"/>
              </svg></a>
          </div>
          <div class="socials__item"><a href="https://www.vk.com/bullosafe" target="_blank" class="link socials__link">
              <svg class="icon icon_vk ">
                <use xlink:href="#icon-vk"/>
              </svg></a>
          </div>
        </div>
      </div>
      <div class="contacts footer__contacts">
        <div class="contacts__phone">
          <div class="contacts__title title title_size_s">Телефон
          </div>
          <div class="contacts__text"><a class="footer-mail-link" href="tel:88005002400">8 800 500 24 00</a>
          </div>
        </div>
        <div class="contacts__email">
          <div class="contacts__title title title_size_s">E-mail
          </div>
          <div class="contacts__text"><a class="footer-mail-link" href="mailto:info@bullo.finance">info@bullo.finance</a>
          </div>
        </div>
      </div>
      <div class="footer__sitemap sitemap">
        <!--<div class="search sitemap__search">
			<div class="search__input">
				<input class="input input_size_s" type="text"/>
				<div class="search__icon">
					<svg class="icon icon_loupe icon_color_gray">
						<use xlink:href="#icon-loupe"></use>
					</svg>
				</div>
			</div>
		</div>-->
        <div class="sitemap__list">
          <div class="sitemap__col">
            <div class="sitemap__item">
              <div class="sitemap__title title title_size_s">Важное
              </div>
              <div class="sitemap__link"><a href="/page/about.html" class="link link_color_gray">О компании</a>
              </div>
              <div class="sitemap__link"><a href="/page/contacts.html" class="link link_color_gray">Контакты</a>
              </div>
            </div>
            <div class="sitemap__item">
              <div class="sitemap__title title title_size_s"> Полезное
              </div>
              <div class="sitemap__link"><a href="/page/work-service.html" class="link link_color_gray">Как это работает?</a>
              </div>
            </div>
          </div>
          <div class="sitemap__col">
            <div class="sitemap__item">
              <!--<div class="sitemap__title title title_size_s">Страховые программы</div>--> 
	            <?= \common\components\Calculator\widgets\common\InsuranceTypeListWidget::widget(['layout' => 'footer']); ?>
            </div>
          </div>
        </div>
        <div class="sitemap__license">
          <div class="sitemap__license-icon">
            <svg class="icon icon_page ">
              <use xlink:href="#icon-page"/>
            </svg>
          </div>
          <div class="sitemap__license-text">Лицензия ЦБ № 345667457456745
          </div>
        </div>
      </div>
    </footer>
  </div>
  <div class="js-scroll-column page__right" style="z-index: auto; position: absolute; top: 0px;">
    <div class="content-how-work active">
      <div class="content-how-work__wrap">
	      <?php echo $model->body ?>
      </div>
    </div>
  </div>
</div>
