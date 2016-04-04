<?php defined('SYSPATH') or die('No direct script access.');
/*
 * Виджет "Меню админа"
 */
class Controller_Widgets_CatalogLast extends Controller_System_Widgets {

    public $template = 'widgets/last_companies';    // Шаблон виждета

    public function action_index()
    {
        $companies = ORM::factory('CatalogCompany')->where('enable','=','1')->and_where('moderate','=','1')->order_by('addtime', 'DESC')->limit(5)->find_all()->as_array('id');
        $photos = ORM::factory('CatalogCompanyPhoto')->companiesPhotoList(array_keys($companies));
        $this->template->set(array(
            'companies' => $companies,
            'photos' => $photos,
        ));
    }

}