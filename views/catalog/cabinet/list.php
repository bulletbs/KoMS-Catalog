<?php defined('SYSPATH') or die('No direct script access.');?>
<h1><?php echo __('My companies') ?></h1>

<?= Flash::render('global/flash') ?>

<a href="<?php echo URL::site().Route::get('catalog_mycompany')->uri(array('action'=>'edit'))?>" class="pure-button pure-button-primary right"><?php echo __('Add company') ?></a>
<br class="clear">
<br class="clear">
<table class="gray content_full">
<thead>
    <tr>
        <th width="3%">№</th>
        <th>Название</th>
        <th width="20%">Действия</th>
    </tr>
</thead>
<tbody>
<?if(count($companies)):?>
<?foreach($companies as $company):?>
    <tr>
        <td><?php echo $company->id?></td>
        <td><?php echo $company->nameLink?></td>
        <td>
            <a href="<?php echo URL::site().Route::get('catalog_mycompany')->uri(array('action'=>'edit', 'id'=>$company->id))?>" class='pure-button pure-button' title="<?php echo __('Edit')?>"><i class="fa fa-edit"></i></a>
            <a href="<?php echo URL::site().Route::get('catalog_mycompany')->uri(array('action'=>'enable', 'id'=>$company->id))?>" class='pure-button pure-button' title="<?php echo __('Status')?>"><i class="fa fa-eye<?php echo !$company->enable ? '-slash' : ''?>"></i></a>
            <a href="<?php echo URL::site().Route::get('catalog_mycompany')->uri(array('action'=>'remove', 'id'=>$company->id))?>" class='pure-button pure-button-error' title="<?php echo __('Delete')?>"><i class="fa fa-trash-o"></i></a>
        </td>
    </tr>
<?endforeach?>
<?else:?>
<tr>
    <td colspan="3" class="message"><?php echo __('You have not created companies') ?></td>
</tr>
<?endif?>
</tbody>
</table>