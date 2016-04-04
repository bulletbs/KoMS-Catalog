<?php defined('SYSPATH') or die('No direct script access.');

if(!Route::cache()){
    Route::set('catalog', 'catalog(/<action>(/<id>)(/p<page>.html))', array('action' => '(main|all|most)', 'id' => '[0-9]+', 'page' => '[0-9]+'))
        ->defaults(array(
            'controller' => 'catalog',
            'action' => 'main',
        ));

    Route::set('catalog_category', 'catalog/<cat_alias>(/p<page>).html', array( 'cat_alias' => '[\d\w\-_]+'))
        ->defaults(array(
            'controller' => 'catalog',
            'action' => 'category',
        ));

    Route::set('catalog_company', 'catalog/<cat_alias>/<id>-<alias>.html', array( 'cat_alias' => '[\d\w\-_]+', 'id' => '[0-9]+', 'alias' => '[\d\w\-_]+'))
        ->defaults(array(
            'controller' => 'catalog',
            'action' => 'company',
        ));

    Route::set('catalog_mycompany', 'profile/companies(/<action>(/<id>)(/p<page>.html))', array('action' => '(list|edit|enable|remove)', 'id' => '[0-9]+', 'page' => '[0-9]+'))
        ->defaults(array(
            'controller' => 'userCompany',
            'action' => 'list',
        ));
}