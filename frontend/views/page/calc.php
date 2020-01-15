<?php
/**
 * @title Калькулятор, главная страница
 */
/* @var $this yii\web\View */
/* @var $model \common\models\Page */

use \common\components\Calculator\widgets\common\HomePageCalculatorWidget;

$this->title = Yii::$app->name;

$model = isset($model)?$model:false;

xj\modernizr\ModernizrAsset::register($this);
?>
<div class="page__inner page__inner_index">
    <div data-scroll-speed="10" class="js-scroll-column page__left page__left_main">
        <?php
        /**
         * @var $active_api_id integer|false
         */

        $active_api_id = isset($active_api_id )? $active_api_id : false;
        ?>
        <div class="insurance-types">
	        <?= HomePageCalculatorWidget::widget(['page' => $model, 'layout' => HomePageCalculatorWidget::LAYOUT_LEFTSIDE ]) ?>
        </div>
        <div class="insurance-company">
            <div class="insurance-company__header">
                <div class="insurance-company__title title">Страховые компании
                </div>
            </div>
            <div class="insurance-company__list">
                <?php foreach(\common\models\Api::find()->where(['enabled' => 1])->orderBy(['name' => SORT_ASC])->all() as $apiModel){ /** @var $apiModel \common\models\Api */ ?>
                    <a href="/company/<?=$apiModel->id?>.html" class="insurance-company__item <?= ($apiModel->id == $active_api_id)?"insurance-company__item_current":""?>">
                        <img src="<?= $apiModel->thumbnail_base_url.'/'.$apiModel->thumbnail_path ?>">
                    </a>
                <?php } ?>
            </div>
        </div>

        <div class="video-banner" data-property="{videoURL:'https://youtu.be/C0tj0MaD_jc', autoPlay: false, containment:'.video-banner', loop: false}">
            <!--<video id="movie" width="100%" height="100%" preload>
                <source src="/video/bullo_final.mp4" />
            </video>-->
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
    </div>
    <div class="js-scroll-column page__right">
        <div class="insurance-find">
	        <?= HomePageCalculatorWidget::widget(['page' => $model, 'layout' => HomePageCalculatorWidget::LAYOUT_RIGHTSIDE]) ?>
        </div>
    </div>
</div>
