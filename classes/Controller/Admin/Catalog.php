<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Catalog extends Controller_Admin_Crud
{
    public $submenu = 'AdminCatalogMenu';

    public $skip_auto_render = array(
        'delete',
        'status',
        'import',
        'importCategory',
    );

    protected $_item_name = 'company';
    protected $_crud_name = 'Site Catalog';

    protected $_model_name = 'CatalogCompany';
    protected $_orderby_field = 'id';
    protected $_orderby_direction = 'DESC';

    public $list_fields = array(
        'id',
        'nameLink',
    );

    protected $_filter_fields = array(
        'category_id' => array(
            'label' => 'Показать категорию',
            'type' => 'select',
        ),
        'name' => array(
            'label' => 'Найти',
            'type' => 'text',
            'oper' => 'like',
        ),
        'id' => array(
            'label' => 'ID',
            'type' => 'text',
            'oper' => '=',
        ),
    );

    public $_form_fields = array(
        'name' => array('type' => 'text'),
        'desc' => array('type' => 'textarea'),
        'category_id' => array(
            'type' => 'select',
            'data' => array('options' => array())
        ),
        'addtime' => array('type' => 'datetime'),
        'enable' => array('type' => 'checkbox'),
        'vip' => array('type' => 'checkbox'),
        'address' => array('type' => 'text'),
        'city' => array('type' => 'text'),
        'state' => array('type' => 'text'),
        'country' => array('type' => 'text'),
        'postcode' => array('type' => 'text'),
        'telephone' => array('type' => 'text'),
        'fax' => array('type' => 'text'),
        'email' => array('type' => 'text'),
        'website' => array('type' => 'text'),
        'photos' => array(
            'type' => 'call_view',
            'data' => 'admin/catalog/photos',
            'advanced_data' => array(
                'photos' => array(),
            )
        ),
//        'meta' => array('type' => 'legend', 'name' => 'Meta tags'),
//        'metakey' => array('type' => 'text'),
//        'metadesc' => array('type' => 'text'),
    );

    protected $_advanced_list_actions = array(
        array(
            'action' => 'status',
            'label' => 'On/Off',
            'icon' => array(
                'field' => 'enable',
                'values' => array(
                    '0' => 'eye-close',
                    '1' => 'eye-open',
                ),
            ),
        ),
    );


    public function action_index()
    {
        /* Filter Parent_id initialize  */
        $this->_filter_fields['category_id']['data']['options'][0] = 'Все категории';
        $this->_filter_fields['category_id']['data']['options'] = array_merge($this->_filter_fields['category_id']['data']['options'], ORM::factory('CatalogCategory')->getOptionList());

        if (!isset($this->_filter_values['category_id']))
            $this->_filter_values['category_id'] = 0;
        $this->_filter_fields['category_id']['data']['selected'] = $this->_filter_values['category_id'];
        $this->_filter_fields['name']['data'] = $this->_filter_values['name'];
        $this->_filter_fields['id']['data'] = $this->_filter_values['id'];

        parent::action_index();
    }

    /**
     * Form preloader
     * @param $model
     * @param array $data
     * @return array|bool|void
     */
    protected function _processForm($model, $data = array())
    {
        /* Setting categories select field */
        $this->_form_fields['category_id']['data']['options'] = ORM::factory('CatalogCategory')->getOptionList();
        $this->_form_fields['category_id']['data']['selected'] = $model->category_id;

        /* Setting photos field */
        $this->_form_fields['photos']['advanced_data']['photos'] = ORM::factory('CatalogCompanyPhoto')->where('company_id', '=', $model->id)->find_all()->as_array('id');
        if (!$model->id) {
            $model->addtime = time();
            $model->enable = true;
        }

        parent::_processForm($model);
    }

    /**
     * Saving Model Method
     * @param $model
     */
    protected function _saveModel($model)
    {
        if (isset($_POST['addtime']))
            $_POST['addtime'] = strtotime($_POST['addtime']);

        parent::_saveModel($model);

        /* Save photos */
        $files = Arr::get($_FILES, 'photos', array('tmp_name' => array()));
        foreach ($files['tmp_name'] as $k => $file) {
            $photo = $model->addPhoto($file);
        }

        /* Deleting photos */
        $files = Arr::get($_POST, 'delphotos', array());
        foreach ($files as $file_id)
            $model->deletePhoto($file_id);

        /* Setting up main photo */
        if(!isset($setmain))
            $setmain = Arr::get($_POST, 'setmain');
        $model->setMainPhoto($setmain);
    }

    /**
     * On/Off item
     */
    public function action_status()
    {
        $company = ORM::factory('CatalogCompany', $this->request->param('id'));
        if ($company->loaded()) {
            $company->flipStatus();
        }
        $this->redirect($this->_crud_uri . URL::query());
    }

    /**
     * Loading model to render form
     * @param null $id
     * @return ORM
     */
    protected function _loadModel($id = NULL)
    {
        $model = ORM::factory($this->_model_name, $id);
//        $this->_form_fields['photo']['data'] = $model->getThumb();

        return $model;
    }

    public function action_import(){
        set_time_limit(600);
        $links = DB::select()->from('jos_mt_links')
            ->where('link_approved' ,'=', 1)
            ->and_where('link_published' ,'=', 1)
            ->as_assoc()
            ->execute();

        foreach($links as $link){
            $company = ORM::factory('CatalogCompany')->where('name', '=', $link['link_name'])->and_where('addtime', '=', strtotime($link['link_created']))->find();
            if(!$company->loaded()){
                $company = ORM::factory('CatalogCompany');
                $company->values($link);
                $company->id = $link['link_id'];
                $company->name = $link['link_name'];
                $company->desc = $link['link_desc'];
                $company->vip = $link['link_featured'];
                $company->addtime = strtotime($link['link_created']);

                $category = DB::select()->from('jos_mt_cl')
                    ->and_where('link_id' ,'=', $link['link_id'])
                    ->as_assoc()
                    ->execute();
                $company->category_id = $category[0]['cat_id'];

                $company->save();
            }

            $images = DB::select()->from('jos_mt_images')
                ->and_where('link_id' ,'=', $link['link_id'])
                ->as_assoc()
                ->execute();
            foreach($images as $image){
                $image_path = DOCROOT."/images/mtree/o/".$image['filename'];
                $company->addPhoto($image_path, $image['ordering'] == 1 ? array('main'=>1) : array());
            }
        }
    }



    /**
     * List items
     */
    public function action_moderate(){
        $this->template->scripts[] = "media/libs/bootstrap/js/bootbox.min.js";
        $this->template->scripts[] = "media/libs/bootstrap/js/bbox_".I18n::$lang.".js";
        $this->template->scripts[] = "media/js/admin/check_all.js";

        $comment_sort = Arr::get($_GET, 'comment_sort', 0);

        $orm = ORM::factory('Comment');
        if(in_array($comment_sort, array(0,1)))
            $orm->where('moderated' ,'=', $comment_sort);
        $count = $orm->count_all();
        $pagination = Pagination::factory(
            array(
                'total_items' => $count,
                'group' => 'admin',
            )
        )->route_params(
                array(
                    'controller' => Request::current()->controller(),
                )
            );
        /**
         * @var $comment ORM
         */
        $orm = ORM::factory('Comment')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset);
        if(in_array($comment_sort, array(0,1)))
            $orm->where('moderated' ,'=', $comment_sort);
        $comments = $orm->find_all();
        $this->template->content
            ->set('pagination', $pagination)
            ->set('comments', $comments)
            ->set('comment_sort', $comment_sort)
            ->set('sorts', array(
                __('Not checked'),
                __('Checked'),
                __('All comments'),
            ))
        ;
    }

    public function action_importCategory(){
//        $category = DB::select()->from('jos_mt_cats')
//            ->and_where('cat_id' ,'>', 0)
//            ->as_assoc()
//            ->execute();
//        foreach($category as $cat){
//            $newcat = ORM::factory('CatalogCategory');
//            $newcat->id = $cat['cat_id'];
//            $newcat->name = $cat['cat_name'];
//            $newcat->alias = $cat['alias'];
//            $newcat->save();
//        }

//        foreach(ORM::factory('CatalogCompany')->find_all() as $company){
////            $company->finalizeSource($company->website);
//            $company->setMainPhoto();
//        }
    }
}
