<?php defined('SYSPATH') or die('No direct script access.'); ?>

    <div id="rating_<?= $company->id ?>" class="rating">
        <input type="hidden" name="val" value="<?= $company->rating ?>"/>
        <input type="hidden" name="votes" value="<?= $company->votes ?>"/>
        <input type="hidden" name="vote-id" value="<?= $company->id ?>"/>
        <input type="hidden" name="cat-id" value="2"/>
    </div>
    <script type="text/javascript">
        $(function () {
            $('.rating').rating({
                fx: 'float',
                image: '/media/libs/rating/images/stars_small.png',
                readOnly: true
            });
        });
    </script>

    <h1><? echo $company->name ?></h1>

    <div class="company_logo<? if (!$logo->loaded()): ?> company_logo_noimg<? endif ?>">
        <? if ($logo->loaded()): ?><?php echo HTML::anchor($logo->getPhotoUri(), $logo->getPreviewTag($company->name), array('data-lightbox' => 'company_gal')) ?><? endif ?>
    </div>
    <ul class="company_info">
        <? if ($company->getCompanyAddress() != ''): ?><li><b>Адрес:</b> <span><?php echo $company->getCompanyAddress()?></li><? endif; ?>
        <? if (!empty($company->telephone)): ?><li><b>Тел.:</b> <span><?= $company->telephone ?></span></li><? endif; ?>
        <? if (!empty($company->fax)): ?><li><b>Факс:</b> <span><?= $company->fax ?></span></li><? endif; ?>
        <? if (!empty($company->email)): ?><li><b>Email:</b> <a id="sendMessage" data-id="<?php echo $company->id?>" href="#"><?php echo __('Send message')?></a><? endif; ?>
        <? if (!empty($company->website)): ?><li><b>Сайт:</b><?= $company->getSourceLink() ?></li><? endif; ?>
    </ul>
    <div class="clear"></div>
    <!--noindex--><div id="mailto"></div><!--/noindex-->
    <div class="right">
        <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
        <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir" data-yashareTheme="counter"></div>
    </div>
    <div class="clear"></div>

<? if (!empty($company->desc)): ?>
    <div class="line_title">
        <h2>Описание компании</h2>
        <div class="clear"></div>
    </div>
    <?php echo nl2br($company->desc) ?>
<? endif; ?>

<? if (count($photos)): ?>
    <div class="line_title">
        <h2>Фотографии</h2>
        <div class="clear"></div>
    </div>
    <ul class="company_gallery">
        <? foreach ($photos as $photo): ?>
            <li><? echo HTML::anchor($photo->getPhotoUri(), $photo->getThumbTag(), array('data-lightbox' => 'company_gal')) ?></li>
        <? endforeach ?>
    </ul>
    <div class="clear"></div>
<? endif; ?>

<? Comments::render($company) ?>
<? Comments::form($company) ?>