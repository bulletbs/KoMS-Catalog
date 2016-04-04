<?php defined('SYSPATH') or die('No direct script access.');
/*
 * Виджет "Меню админа"
 */
class Controller_Widgets_AdminCatalogMenu extends Controller_System_Widgets {

    public $template = 'widgets/adminsubmenu';    // Шаблон виждета

    public function action_index()
    {
        $select = lcfirst(Request::initial()->controller());
        $menu = array(
            'Компании' => array('catalog'),
            'Категории' => array('catalogCategory'),
            'Проверка компаний' => array('catalogModerate'),
        );

        // Вывод в шаблон
        $this->template->menu = $menu;
        $this->template->select = $select;
    }

}