<?php
  /**
   * @var $active_api_id integer|false
   */

  $active_api_id = isset( $active_api_id )? $active_api_id : false;
?>
<div class="insurance-types">
	<ul class="insurance-types__row">
		<li class="insurance-types__item   active ">
			<a class="insurance-types__wrap" href="/page/travel-insurance-form.html">
				<div class="insurance-types__icon">
					<svg class="icon icon_travel ">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#m-travel"></use>
					</svg>
				</div>
				<div class="insurance-types__link link link_color_black link_line_solid">
					Страхование путешественников					</div>
				<div class="insurance-types__green-block">
					<span>Страхование путешественников</span>
				</div>
			</a>
		</li>
		<li class="insurance-types__item   active ">
			<a class="insurance-types__wrap" href="/page/accident-insurance-form.html">
				<div class="insurance-types__icon">
					<svg class="icon icon_accident ">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#m-accident"></use>
					</svg>
				</div>
				<div class="insurance-types__link link link_color_black link_line_solid">
					Страхование от несчастных случаев					</div>
				<div class="insurance-types__green-block">
					<span>Страхование от несчастных случаев</span>
				</div>
			</a>
		</li>
		<li class="insurance-types__item   active ">
			<a class="insurance-types__wrap" href="/page/borrower-insurance-form.html">
				<div class="insurance-types__icon">
					<svg class="icon icon_borrower ">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#m-borrower"></use>
					</svg>
				</div>
				<div class="insurance-types__link link link_color_black link_line_solid">
					Страхование жизни заемщика ипотеки					</div>
				<div class="insurance-types__green-block">
					<span>Страхование жизни заемщика ипотеки</span>
				</div>
			</a>
		</li>
		<li class="insurance-types__item   active ">
			<a class="insurance-types__wrap" href="/page/property-insurance-form.html">
				<div class="insurance-types__icon">
					<svg class="icon icon_property ">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#m-property"></use>
					</svg>
				</div>
				<div class="insurance-types__link link link_color_black link_line_solid">
					Страхование имущества					</div>
				<div class="insurance-types__green-block">
					<span>Страхование имущества</span>
				</div>
			</a>
		</li>
		<li class="insurance-types__item   active ">
			<a class="insurance-types__wrap" href="/page/mortgage-insurance-form.html">
				<div class="insurance-types__icon">
					<svg class="icon icon_mortgage ">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#m-mortgage"></use>
					</svg>
				</div>
				<div class="insurance-types__link link link_color_black link_line_solid">
					Страхование ипотеки					</div>
				<div class="insurance-types__green-block">
					<span>Страхование ипотеки</span>
				</div>
			</a>
		</li>
		<li class="insurance-types__item   active ">
			<a class="insurance-types__wrap" href="/page/endowment-life-insurance-form.html">
				<div class="insurance-types__icon">
					<svg class="icon icon_endowment ">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#m-endowment"></use>
					</svg>
				</div>
				<div class="insurance-types__link link link_color_black link_line_solid">
					Накопительное страхование жизни					</div>
				<div class="insurance-types__green-block">
					<span>Накопительное страхование жизни</span>
				</div>
			</a>
		</li>
		<li class="insurance-types__item   active ">
			<a class="insurance-types__wrap" href="/page/investment-life-insurance-form.html">
				<div class="insurance-types__icon">
					<svg class="icon icon_investment ">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#m-investment"></use>
					</svg>
				</div>
				<div class="insurance-types__link link link_color_black link_line_solid">
					Инвестиционное страхование жизни					</div>
				<div class="insurance-types__green-block">
					<span>Инвестиционное страхование жизни</span>
				</div>
			</a>
		</li>
		<li class="insurance-types__item  insurance-types__item_disabled passive ">
			<a class="insurance-types__wrap" href="#">
				<div class="insurance-types__icon">
					<svg class="icon icon_auto ">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#m-auto"></use>
					</svg>
				</div>
				<div class="insurance-types__link link link_color_black link_line_solid">
					Автострахование					</div>
				<div class="insurance-types__green-block">
					<span>Автострахование</span>
				</div>
			</a>
		</li>
	</ul>
</div>
<div class="insurance-company">
	<div class="insurance-company__header">
		<div class="insurance-company__title title">Страховые компании
		</div>
	</div>
	<div class="insurance-company__list">
    <?php foreach(\common\models\Api::find()->where(['enabled' => 1])->orderBy(['name' => SORT_ASC])->all() as $model){ /** @var $model \common\models\Api */ ?>
      <a href="/company/<?=$model->id?>.html" class="insurance-company__item <?= ($model->id == $active_api_id)?"insurance-company__item_current":""?>">
        <img src="<?= $model->thumbnail_base_url.'/'.$model->thumbnail_path ?>">
      </a>
    <?php } ?>
	</div>
</div>



<div class="video-banner">
  <!---<video id="movie" width="100%" height="100%" preload>
    <source src="/video/bullo_final.mp4" />
  </video>--->
  <div class="video-background"></div>
	<svg class="icon icon_play-video">
		<use xlink:href="#icon-play-video"></use>
	</svg>
</div>

<div class="how-it-work">
  <div class="how-it-work__item">
    <div class="how-it-work__info">
      <div class="how-it-work__title title">Предлагаем оформить страховку онлайн</div>
      <div class="how-it-work__text">
				Воспользуйтесь нашим сайтом, чтобы узнать о видах страхования, заказать наиболее подходящий вариант и оплатить полис одним из предложенных способов. Электронный документ будет немедленно отправлен вам на электронную почту. Он действителен и принимается в любых визовых центрах.
			</div>
    </div>
    <div class="how-it-work__icon">
      <svg class="icon icon_how-it-works ">
        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-how-it-works"></use>
      </svg>
    </div>
  </div>
  <div class="how-it-work__item">
    <div class="how-it-work__info">
      <div class="how-it-work__title title">С нами легко и выгодно
      </div>
      <div class="how-it-work__text">
        В последние годы продажа полисов через интернет получила широкое распространение. Наше преимущество в том, что мы предлагаем купить страховой полис онлайн по действующим тарифам страховых агентств. Лидирующие позиции и высокая конкуренция на рынке позволяют нам выставлять зачастую более низкие цены, чем другие компании.
      </div>
    </div>
    <div class="how-it-work__icon">
      <svg class="icon icon_labirint">
        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#labirint"></use>
      </svg>
    </div>
  </div>
	<div class="how-it-work__item">
		<div class="how-it-work__info">
			<div class="how-it-work__title title">Подберем лучшие условия страхования</div>
			<div class="how-it-work__text">
        У нас можно сделать страховку онлайн на любой жизненный случай, сравнить предложения различных страховых компаний и посоветовать оптимальный тариф. Вам не придется тратить время на поиски информации и переплачивать за ненужные опции. Умная система фильтров на нашем сайте позволит вам сэкономить не только деньги, но и время.
      </div>
		</div>
    <div class="how-it-work__icon">
			<svg class="icon icon_libra">
				<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#libra"></use>
			</svg>
		</div>
	</div>
	<div class="how-it-work__item">
		<div class="how-it-work__info">
			<div class="how-it-work__title title">Мы против переплат и комиссий</div>
			<div class="how-it-work__text">С нами вы платите только за оформление страхового полиса. Комиссия за использование нашего сервиса не взимается. Мы гарантируем, что ваша страховка будет стоить столько же, сколько на официальном сайте страхователя, а иногда и дешевле: мы действительно можем себе позволить больше, чем остальные онлайн-сервисы.</div>
		</div>
    <div class="how-it-work__icon">
			<svg class="icon icon_overpayments">
				<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#overpayments"></use>
			</svg>
		</div>
	</div>
	<div class="how-it-work__item">
		<div class="how-it-work__info">
			<div class="how-it-work__title title">С заботой о каждом клиенте</div>
			<div class="how-it-work__text">Мы находимся в курсе актуальных тенденций в сфере онлайн-страхования, отслеживаем свежие предложения на рынке и сравниваем их между собой. Все это делаем с искренней поддержкой и заботой о клиенте. Ровно так, как делали бы для себя. Именно поэтому клиенты нам доверяют и рекомендуют своим друзьям и знакомым купить страховку у нас.</div>
		</div>
    <div class="how-it-work__icon">
			<svg class="icon icon_support">
				<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#support"></use>
			</svg>
		</div>
	</div>
</div>

<footer class="footer page__footer">
	<div class="footer__social">
		<div class="footer__logo">
			<div class="footer__logo-icon">
				<svg class="icon icon_logo ">
					<use xlink:href="#icon-logo"></use>
				</svg>
			</div>
			<div class="footer__logo-text">© 2016-<?= date('Y')?> <br> BulloSafe
			</div>
		</div>
		<div class="footer__social-list socials">
			<div class="socials__item"><a href="https://www.facebook.com/bullosafe" target="_blank" class="link socials__link">
					<svg class="icon icon_fb ">
						<use xlink:href="#icon-fb"></use>
					</svg></a>
			</div>
			<div class="socials__item"><a href="https://www.vk.com/bullosafe" target="_blank" class="link socials__link">
					<svg class="icon icon_vk ">
						<use xlink:href="#icon-vk"></use>
					</svg></a>
			</div>
			<!--<div class="socials__item"><a href="#" class="link socials__link">-->
			<!--<svg class="icon icon_gplus ">-->
			<!--<use xlink:href="#icon-gplus"></use>-->
			<!--</svg></a>-->
			<!--</div>-->
			<!--<div class="socials__item"><a href="#" class="link socials__link">-->
			<!--<svg class="icon icon_twi ">-->
			<!--<use xlink:href="#icon-twi"></use>-->
			<!--</svg></a>-->
			<!--</div>-->
			<!--<div class="socials__item"><a href="#" class="link socials__link">-->
			<!--<svg class="icon icon_youtube ">-->
			<!--<use xlink:href="#icon-youtube"></use>-->
			<!--</svg></a>-->
			<!--</div>-->
			<!--<div class="socials__item"><a href="#" class="link socials__link">-->
			<!--<svg class="icon icon_in ">-->
			<!--<use xlink:href="#icon-in"></use>-->
			<!--</svg></a>-->
			<!--</div>-->
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
			<div class="contacts__text">
				<a class="footer-mail-link" href="mailto:info@bullo.finance">info@bullo.finance</a>
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
					<!--<div class="sitemap__link"><a href="/page/partnership.html" class="link link_color_gray">Партнеры</a>
					</div>-->
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
					    <div class="sitemap__title title title_size_s">Страховые программы</div>
					    <div class="sitemap__link"><a href="/page/travel-insurance-form.html" class="link link_color_gray">Туристическое страхование</a></div>
              <div class="sitemap__link"><a href="/page/endowment-life-insurance-form.html" class="link link_color_gray">Страхование жизни</a></div>
              <div class="sitemap__link"><a href="/page/property-insurance-form.html" class="link link_color_gray">Страхование имущества</a></div>
              <div class="sitemap__link"><a href="/page/borrower-insurance-form.html" class="link link_color_gray">Страхование заемщика ипотеки</a></div>
              <div class="sitemap__link"><a href="/page/mortgage-insurance-form.html" class="link link_color_gray">Страхование залогового имущества</a></div>
							<div class="sitemap__link"><a href="/page/investment-life-insurance-form.html" class="link link_color_gray">Инвестиционное страхование жизни</a></div>
				  </div>
			</div>
		</div>
		<div class="sitemap__license">
			<div class="sitemap__license-icon">
				<svg class="icon icon_page ">
					<use xlink:href="#icon-page"></use>
				</svg>
			</div>
			<div class="sitemap__license-text">Лицензия ЦБ № 345667457456745</div>
		</div>
	</div>
</footer>

