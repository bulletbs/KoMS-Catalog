<?php defined('SYSPATH') or die('No direct script access.');?>


<div class="pure-menu pure-menu-open">
    <b class="pure-menu-heading">Сферы деятельности</b>
    <ul>
    <?foreach($categories as $cat):?>
        <li<?if($active_alias == $cat->alias):?> class="active"<?endif?>><?php echo HTML::anchor($cat->getUri(), $cat->name)?></li>
    <?endforeach?>
    </ul>
</div>