<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Котроллер для вывода главной страницы НОВОСТЕЙ
 */

class Controller_Catalog extends Controller_System_Page
{
    const MOST_CONTENT_INTERVAL_DAYS = 30;

    const MAIN_PAGE_CACHE = 'main_page';
    const MAIN_PAGE_CACHE_TIME = 1800;

    public $skip_auto_render = array(
        'most',
        'similar',
        'goto',
    );

    public $skip_auto_content_apply = array(
        'main',
    );

    public $categories;

    protected $cfg;

    public function before(){
        parent::before();

        $this->categories = Model_CatalogCategory::getCategoriesList();
        $this->styles[] = "media/css/catalog.css";
        $this->styles[] = "media/libs/pure-release-0.5.0/menus.css";
        $this->scripts[] = "media/libs/rating/jquery.rating-2.0.min.js";
        $this->styles[] = "media/libs/rating/jquery.rating.css";

        if($this->auto_render){
            $this->breadcrumbs->add('Каталог', '/catalog', 1);

            $this->template->right_column = View::factory('catalog/right_column', array(
                'categories' => $this->categories,
                'active_alias' => Request::current()->param('cat_alias'),
            ));
        }
        $this->cfg = Kohana::$config->load('catalog')->as_array();
    }

    /**
     * HMVC action for rendering catalog on mainpage
     */
    public function action_main(){
//        Cache::instance()->delete(self::MAIN_PAGE_CACHE);
//        if(!$content = Cache::instance()->get(self::MAIN_PAGE_CACHE)){

            /* META */
            $this->title = $this->cfg['main_title'];

            /* Init Pagination module */
            $count = ORM::factory('CatalogCompany')->where('enable','=','1')->count_all();
            $pagination = Pagination::factory(array(
                'total_items' => $count,
                'group' => 'catalog',
            ))->route_params(array(
                'controller' => Request::current()->controller(),
            ));
            $companies = ORM::factory('CatalogCompany')->order_by('vip', 'desc')->where('enable','=','1')->order_by('addtime', 'DESC')->offset($pagination->offset)->limit($pagination->items_per_page)->find_all()->as_array('id');
            $photos = ORM::factory('CatalogCompanyPhoto')->companiesPhotoList(array_keys($companies));

            $content = View::factory('catalog/main')
                ->set('companies', $companies)
                ->set('photos', $photos)
                ->set('categories', $this->categories)
                ->set('pagination', $pagination)
                ->render()
            ;
//            Cache::instance()->set(self::MAIN_PAGE_CACHE, $content, self::MAIN_PAGE_CACHE_TIME);
//        }
        $this->template->content = $content;
    }

    /**
     *  Output all category articles
     */
    public function action_category(){
        $alias = $this->request->param('cat_alias');
        $id = Model_CatalogCategory::getCategoryIdByAlias($alias);
        if($id){
            if(!isset($this->categories[$id]))
                $this->redirect('catalog');
            $category = $this->categories[$id];
//            $this->breadcrumbs->add(Model_CatalogCategory::$parts[$category->part_id], Model_CatalogCategory::getPartUri($category->part_id));

            /* Meta tags */
            $this->title = htmlspecialchars( $category->name .' - '.$this->config->view['title']);

            /* Init Pagination module */
            $count = ORM::factory('CatalogCompany')->where('category_id','=', $id)->and_where('enable','=','1')->count_all();
            $pagination = Pagination::factory(array(
                'total_items' => $count,
                'group' => 'catalog',
            ))->route_params(array(
                'controller' => Request::current()->controller(),
                'cat_alias'=>$alias,
            ));

            $companies = ORM::factory('CatalogCompany')->where('category_id','=', $id)->and_where('enable','=','1')->order_by('vip', 'desc')->order_by('addtime', 'DESC')->offset($pagination->offset)->limit($pagination->items_per_page)->find_all()->as_array('id');
            $photos = ORM::factory('CatalogCompanyPhoto')->companiesPhotoList(array_keys($companies));

            $this->template->content
                ->set('categories', array($id=>$category))
                ->set('category', $category->name)
                ->set('companies', $companies)
                ->set('photos', $photos)
                ->set('pagination', $pagination)
            ;
        }
        else{
            $this->redirect('catalog');
        }
    }

    /**
     * Company output
     * @throws HTTP_Exception_404
     */
    public function action_company(){
        $id = $this->request->param('id');
        $company = ORM::factory('CatalogCompany', $id);
        if($company->loaded() && $company->enable==1){
            /* Views increment */
            DB::update($company->table_name())->set(array('views'=>DB::expr('views+1')))->where('id', '=', $id)->execute();

            /* breadcrumbs & similar articles */
            if(isset($this->categories[$company->category_id]))
                $this->breadcrumbs->add($this->categories[$company->category_id]->name, $this->categories[$company->category_id]->getUri(), 3);

            /* Meta tags */
            $this->title = htmlspecialchars( !empty($company->title) ? $company->title : $company->name .' - '.$this->config->view['title'], ENT_QUOTES);
            $this->description = htmlspecialchars( substr( strip_tags($company->desc) , 0, 255), ENT_QUOTES);
//            $this->keywords = !empty($company->keywords) ? $company->keywords : $this->config->view['keywords'];

            /* Photos */
            $logo = $company->photos->where('main', '=', 1)->find();
            $photos = $company->photos->where('main', '=', 0)->find_all()->as_array('id');

            /* Libs */
            $this->styles[] = "media/libs/pure-release-0.5.0/forms.css";
            $this->styles[] = "media/libs/lightbox/lightbox.css";
            $this->scripts[] = "media/libs/lightbox/lightbox.js";
            $this->scripts[] = 'media/js/catalog/company.js';
            $this->template->content
                ->set('logo', $logo)
                ->set('photos', $photos)
                ->set('company', $company);
        }
        else{
            throw new HTTP_Exception_404('Requested page not found');
        }
    }

    /**
     * Отображение формы отправки сообщения (AJAX)
     */
    public function action_send_message(){
        if(!$this->request->is_ajax())
            $this->go(Route::get('catalog')->uri());
        $id = $this->request->param('id');
        $company = ORM::factory('CatalogCompany', $id);
        if($company->loaded()){
            $this->json['status'] = TRUE;
            $errors = array();
            if($this->request->method() == Request::POST){
                $validation = Validation::factory($_POST)
                    ->rule('email', 'not_empty')
                    ->rule('email', 'email', array(':value'))
                    ->rule('text', 'not_empty')
                    ->rule('text', 'min_length', array(':value',10))
                    ->rule('text', 'max_length', array(':value',1000))
                    ->labels(array(
                        'email' => __('Your e-mail'),
                        'text' => __('Message text'),
                        'captcha' => __('Enter captcha code'),
                    ))
                ;
                if(!$this->logged_in)
                    $validation->rules('captcha', array(
                        array('not_empty'),
                        array('Captcha::checkCaptcha', array(':value', ':validation', ':field'))
                    ));
                if($validation->check()){
                    Email::instance()
                        ->to($company->email)
                        ->from($this->config->robot_email)
                        ->subject($this->config['project']['name'] .': '. __('Message from catalog'))
                        ->message(View::factory('catalog/company_mailto_letter', array(
                                'name' => $company->name,
                                'email'=> Arr::get($_POST, 'email'),
                                'text'=> strip_tags(Arr::get($_POST, 'text')),
                                'site_name'=> $this->config['project']['name'],
                            ))->render()
                            , true)
                        ->send();
                    Flash::success(__("Your message successfully sended"));
                    $this->json['content'] = Flash::render('global/flash');
                    return;
                }
                else
                    $errors = $validation->errors('error/validation');
            }
            $this->json['content'] = View::factory('catalog/company_mailto')->set(array(
                'errors' => $errors,
                'company_id' => $company->id,
            ))->render();
        }
    }


    /**
     * Redirection to article source
     * @throws HTTP_Exception_404
     */
    public function action_goto(){
        $id = $this->request->param('id');
        $company = ORM::factory('CatalogCompany', $id);
        if($company->loaded() && $company->enable==1 && !empty($company->website)){
            $company->gotoSource();
        }
        else{
            throw new HTTP_Exception_404('Requested page not found');
        }
    }
}