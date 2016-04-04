<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_CatalogCategory extends Controller_Admin_Crud
{
    public $submenu = 'AdminCatalogMenu';

    protected $_item_name = 'category';
    protected $_crud_name = 'Catalog Categories';

    protected $_model_name = 'CatalogCategory';

    public $list_fields = array(
        'id',
//        'partname',
        'name',
    );

    public $_form_fields = array(
        'name' => array('type'=>'text'),
        'alias' => array('type'=>'text'),
//        'part_id' => array(
//            'type'=>'select',
//            'data'=>array('options'=>array())
//        ),
    );


    /**
     * Form preloader
     * @param $model
     * @param array $data
     * @return array|bool|void
     */
    protected function _processForm($model, $data = array()){
//        $this->_form_fields['part_id']['data']['options'] = $model::$parts;
//        $this->_form_fields['part_id']['data']['selected'] = $model->part_id;

        parent::_processForm($model);
    }

    /**
     * Loading model to render form
     * @param null $id
     * @return ORM
     */
    protected function _loadModel($id = NULL){
        $model = ORM::factory($this->_model_name, $id);
        return $model;
    }
}
