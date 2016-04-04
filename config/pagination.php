<?php

return array(

    'news' => array(
        'total_items'       => 0,
        'items_per_page'    => 15,
        'current_page'      => array
        (
            'source'        => 'route',
            'key'           => 'page',
        ),
        'view'              => 'catalog/pagination',
        'auto_hide'         => TRUE,
        'first_page_in_url' => FALSE,
        'count_in' => 3,
        'count_out' => 3,
    ),
);