<?php defined('SYSPATH') or die('No direct script access.');?>
<h1><?php echo __('Company edit')?></h1>
<?=Form::open(URL::site(Route::get('catalog_mycompany')->uri(array('action'=>'edit', 'id'=>$model->id))), array('class' => 'pure-form pure-form-stacked', 'enctype'=>'multipart/form-data'))?>
    <fieldset>
        <?if(count($errors)):?><?= View::factory('error/validation', array('errors' => $errors))->render()?><?endif;?>
        <?=Form::hidden('id', $model->id, array('class' => ''));  ?>

        <legend><?php echo  __('Company info')?></legend>
        <?=Form::label('name', __('Company Name'));  ?>
        <?=Form::input('name', $model->name, array('class' => ''));  ?>
        <?=Form::label('category_id', __('Category'));  ?>
        <?=Form::select('category_id', $categories, $model->category_id, array('class' => ''));  ?>
        <?=Form::label('desc', __('Company description'));  ?>
        <?=Form::textarea('desc', $model->desc, array('class' => ''));  ?>
        <?=Form::label('enable',' ');  ?>
        <?=Form::checkbox('enable', 1, $model->enable==1, array('class' => '')) .' '. __('Status');  ?>
        <legend><?php echo  __('Company contacts')?></legend>
        <div class="pure-g">
            <div class="pure-u-1-2">
                <?=Form::label('country', __('Country'));  ?>
                <?=Form::input('country', $model->country, array('class' => ''));  ?>
                <?=Form::label('city', __('City'));  ?>
                <?=Form::input('city', $model->city, array('class' => ''));  ?>
                <?=Form::label('state', __('Region'));  ?>
                <?=Form::input('state', $model->state, array('class' => ''));  ?>
                <?=Form::label('address', __('Address'));  ?>
                <?=Form::input('address', $model->address, array('class' => ''));  ?>
                <?=Form::label('postcode', __('Postcode'));  ?>
                <?=Form::input('postcode', $model->postcode, array('class' => ''));  ?>
            </div>
            <div class="pure-u-1-2">
                <?=Form::label('telephone', __('Phone'));  ?>
                <?=Form::input('telephone', $model->telephone, array('class' => ''));  ?>
                <?=Form::label('fax', __('Fax'));  ?>
                <?=Form::input('fax', $model->fax, array('class' => ''));  ?>
                <?=Form::label('email', __('Email'));  ?>
                <?=Form::input('email', $model->email, array('class' => ''));  ?>
                <?=Form::label('website', __('Website'));  ?>
                <?=Form::input('website', $model->website, array('class' => ''));  ?>
            </div>
        </div>
        <legend><?php echo  __('Company images')?></legend>

        <?if(count($photos)):?>
            <?foreach($photos as $photo):?>
                <div class="pure-u-4-24">
                    <?= HTML::anchor($photo->getPhotoUri(), $photo->getThumbTag('',array('class'=>'thumbnail')), array('target'=>'_blank')) ?>
                    <?= FORM::checkbox('delphotos[]', $photo->id, FALSE)?> удалить<br>
                    <?= FORM::radio('setmain', $photo->id, $photo->main == 1)?> логотип
                </div>
            <?endforeach;?>
             <legend></legend>
        <?endif?>
        <div class="pure-g">
        <?for($i=1; $i < 3; $i++):?>
        <div class="pure-u-1-2">
            <?php echo Form::radio('setNewMain', $i, FALSE, array('title'=>'Выбрать логотип')) ?>
            <?php echo Form::file('photos['.$i.']', array('class'=>'form-control', 'id'=>'photos_'.$i)) ?>
        </div>
        <?endfor?>
        </div>
        <br>

        <legend></legend>
        <?=Form::submit('register', __('Save'), array('type'=>'submit', 'class' => 'pure-button pure-button-primary'));  ?>
        <?=Form::submit('cancel', __('Cancel'), array('type'=>'submit', 'class' => 'pure-button'));  ?>
    </fieldset>
<?=Form::close()?>