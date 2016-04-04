<?php defined('SYSPATH') or die('No direct script access.');
/*
 * Виджет "Меню админа"
 */
class Controller_Widgets_LastCompanies extends Controller_System_Widgets {

    public $template = 'widgets/last_companies';    // Шаблон виждета

    public function action_index()
    {
        $companies = ORM::factory('CatalogCompany')->where('enable','=','1')->and_where('','','')->order_by('')->find_all()->as_array('id');
        $this->template->set(array(
            'companies' => $companies,
        ));
    }

}