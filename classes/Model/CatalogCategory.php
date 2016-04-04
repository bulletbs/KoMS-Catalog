<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Created by JetBrains PhpStorm.
 * User: butch
 * Date: 09.02.14
 * Time: 19:19
 */

class Model_CatalogCategory extends ORM{

    const CATEGORY_CACHE_TIME = 86400;
    const CATEGORY_OPTIONS_CACHE = 'catalog_category_options_cache';
    const CATEGORY_TREE_CACHE = 'catalog_category_tree_cache';
    const CATEGORY_LIST_CACHE = 'catalog_category_list_cache';
    CONST CATEGORY_MENUARRAY_CACHE = 'catalog_category_menu_array';

    protected $_table_name = 'catalog_categories';
    protected $_reload_on_wakeup   = FALSE;

    protected $_uriToMe;

    public static $parts = array(
//        0=>'Root',
    );

    public static $parts_uri = array(
        'root'=>0,
    );

    public function rules(){
        return array(
            'name' => array(
                array('not_empty'),
                array('min_length', array('value:',3)),
                array('max_length', array('value:',50)),
            ),
        );
    }

    public function labels(){
        return array(
            'id' => __('Id'),
            'name' => __('Name'),
            'alias' => __('Alias'),
//            'part_id' => __('Part'),
//            'partname' => __('Part Name'),
        );
    }


    public function filters(){
        return array(
            'alias' => array(
                array(array($this,'generateAlias'))
            ),
        );
    }

    /**
     * Generate transliterated alias
     */
    public function generateAlias($alias){
        $alias = trim($alias);
        if(empty($alias))
            $alias = Text::transliterate($this->name, true);
        return $alias;
    }

    /**
     * Getting category list
     * @return array|mixed
     */
    public static function getCategoriesList(){
        if(!$categories = Cache::instance()->get(Model_CatalogCategory::CATEGORY_LIST_CACHE)){
            $categories = ORM::factory('CatalogCategory')->find_all()->as_array('id');
            Cache::instance()->set(Model_CatalogCategory::CATEGORY_LIST_CACHE, $categories, Model_CatalogCategory::CATEGORY_CACHE_TIME);
        }
        return $categories;
    }

    /**
     * Getting category ID by Alias
     * @param string $alias
     * @return int|null
     */
    public static function getCategoryIdByAlias($alias){
        $categories = self::getCategoriesList();
        foreach($categories as $category)
            if($category->alias == $alias)
                return $category->id;
        return NULL;
    }

    /**
     * Getting category ID by Alias
     * @param string $alias
     * @return int|null
     */
    public static function getPartIdByAlias($alias){
        if(isset(self::$parts_uri[$alias]))
            return self::$parts_uri[$alias];
        return NULL;
    }

    /**
     * Getting category list by part_id
     * @param $part_id
     * @return array
     */
    public static function getCategoriesByPart($part_id){
        $result = array();
        $categories = self::getCategoriesList();
        foreach($categories as $category){
            if($category->part_id == $part_id)
                $result[$category->id] = $category;
        }
        return $result;
    }

    /**
     * Getting category options list for HTML::select
     * @return array|mixed
     */
    public function getOptionList(){
        if(!$options = Cache::instance()->get(Model_CatalogCategory::CATEGORY_OPTIONS_CACHE)){
            $options = array();
            $categories = ORM::factory('CatalogCategory')->find_all();
            foreach($categories as $category)
                $options[$category->id] = $category->name;
//                $options[Model_CatalogCategory::$parts[$category->part_id]][$category->id] = $category->name;
            Cache::instance()->set(Model_CatalogCategory::CATEGORY_OPTIONS_CACHE, $options, Model_CatalogCategory::CATEGORY_CACHE_TIME);
        }
        return $options;
    }

    /**
     * Adds getting partname value
     * @param string $column
     * @return mixed|null
     */
    public function get($column){
        if($column == 'partname'){
            if(!is_null($this->part_id) && isset(Model_CatalogCategory::$parts[$this->part_id]))
                return Model_CatalogCategory::$parts[$this->part_id];
            return NULL;
        }
        else
            return parent::get($column);
    }

    /**
     * Get part url by part ID
     * @param $id
     * @return string
     */
    public static function getPartUri($id){
        $parts_uris = array_flip(Model_CatalogCategory::$parts_uri);
        return Route::get('news_part')->uri(array(
            'part_alias' => $parts_uris[$id],
        ));
    }

    /**
     * Get category url
     * @return string
     */
    public function getUri(){
        if(is_null($this->_uriToMe)){
//            $parts_uris = array_flip(Model_CatalogCategory::$parts_uri);
            $this->_uriToMe = Route::get('catalog_category')->uri(array(
//                'part_alias' => $parts_uris[$this->part_id],
                'cat_alias' => $this->alias,
            ));
        }
        return $this->_uriToMe;
    }
    /**
     * Request module categories links array for sitemap generation
     * @return array
     */
    public function sitemapCategories(){
        $links = array();
        foreach($this->getCategoriesList() as $key=>$model){
            $links[] = $model->getUri();
            $count = ORM::factory('CatalogCompany')->where('category_id','=', $key)->and_where('enable','=','1')->count_all();
            $pagination = Pagination::factory(array('group' => 'catalog', 'total_items'=>$count), Request::factory());
            $route = Route::get('catalog_category');
            for ($i = 2; $i <= $pagination->total_pages; $i++)
                $links[] = $route->uri(array(
                    'cat_alias' => $model->alias,
                    'page' => $i,
                ));
        }
        return $links;
    }
}