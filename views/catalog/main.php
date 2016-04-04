<?php defined('SYSPATH') or die('No direct script access.');?>
<h1>Каталог компаний</h1>

<?foreach($companies as $company):?>
<div class="company_list_item <?if($company->vip):?> vipstyle<?endif?>">
    <div class="company_logo_prev<?if(!isset($photos[$company->id])):?> company_logo_prev_noimg<?endif?>">
        <?if(isset($photos[$company->id])):?><?php echo HTML::anchor($company->getUri(), $photos[ $company->id ]->getThumbTag())?> <?endif?>
    </div>
    <div id="rating_<?= $company->id ?>" class="rating">
        <input type="hidden" name="val" value="<?= $company->rating ?>"/>
        <input type="hidden" name="votes" value="<?= $company->votes ?>"/>
        <input type="hidden" name="vote-id" value="<?= $company->id ?>"/>
        <input type="hidden" name="cat-id" value="2"/>
    </div>
    <h3><?php echo HTML::anchor($company->getUri(), $company->name) ?></h3>
    <?if(isset($categories[$company->category_id])):?><small class="quiet"><?php echo HTML::anchor($categories[$company->category_id]->getUri(), $categories[$company->category_id]->name)?></small> <?endif?>
    <div class="company_list_contacts">
        <?if($company->getCompanyAddress() != ''):?><b>Адрес:</b> <?php echo $company->getCompanyAddress()?><br><?endif;?>
        <?if(!empty($company->telephone)):?><b>Тел.:</b> <?= $company->telephone ?><br><?endif;?>
        <?if(!empty($company->fax)):?><b>Факс:</b> <?= $company->fax ?><br><?endif;?>
<!--        --><?//if(!empty($company->email)):?><!--<b>Email:</b> <a rel="nofollow" href="mailto:--><?//= $company->email?><!--">--><?//= $company->email?><!--</a><br>--><?//endif;?>
        <?if(!empty($company->website)):?><b>Сайт:</b> <?= $company->getSourceLink()?><br><?endif;?>
    </div>
    <div class="clear"></div>
</div>
<?endforeach?>
<?= $pagination->render()?>

<script type="text/javascript">
    $(function () {
        $('.rating').rating({
            fx: 'float',
            image: '/media/libs/rating/images/stars_small.png',
            readOnly: true
        });
    });
</script>