<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Created by JetBrains PhpStorm.
 * User: butch
 * Date: 23.05.12
 * Time: 18:35
 * To change this template use File | Settings | File Templates.
 */
class Controller_Admin_CatalogModerate extends Controller_Admin_Moderate
{
    protected $submenu = 'AdminCatalogMenu';
    protected $_crud_uri = 'admin/catalog';

    public $model_name = 'CatalogCompany';
    public $moderate_field = 'moderate';

    protected $_item_name = 'компания';
    protected $_moderate_name = 'Проверка компаний';

    public $list_fields = array(
        'nameLink',
    );
}
