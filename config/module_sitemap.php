<?php defined('SYSPATH') or die('No direct script access.');
return array(
    array(
        'name' => 'catalog',
        'priority' => '0.5',
        'sources' =>array(
            array(
                'model' => 'CatalogCategory',
                'get_links_method' => 'sitemapCategories',
            ),
            array(
                'model' => 'CatalogCompany',
                'get_links_method' => 'sitemapCompanies',
            ),
        )
    )

);
