<?php defined('SYSPATH') or die('No direct script access.');?>


<div class="tabber">
    <ul class="tabs">
        <li class="active"><a href="<?php echo Route::get('catalog')->uri()?>" class="active">Последние компании</a></li>
    </ul>
    <div class="panel-container">
        <div class="panel" style="display: block;">
        <?foreach($companies as $company):?>
        <div class="item">
            <?php echo isset($photos[$company->id]) ?  HTML::anchor($company->getUri(), $photos[ $company->id ]->getThumbTag($company->name)) : HTML::image(  'media/css/images/noimage_100.png', array('alt'=>$company->name)) ?>
            <div><?php echo HTML::anchor($company->getUri(), $company->name)?></div>
        </div>
        <?endforeach?>
        </div>
    </div>
</div>