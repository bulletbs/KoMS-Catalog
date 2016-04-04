<?php defined('SYSPATH') or die('No direct script access.');

class Model_CatalogCompany extends ORM{

    public $image;
    public $thumb;

    protected $_table_name = 'catalog_company';

    protected $_uriToMe;

    protected $_belongs_to = array(
        'user' => array(
            'model' => 'User',
            'foreign_key' => 'user_id',
        ),
        'category' => array(
            'model' => 'CatalogCategory',
            'foreign_key' => 'category_id',
        ),
    );

    protected $_has_many = array(
        'photos' => array(
            'model' => 'CatalogCompanyPhoto',
            'foreign_key' => 'company_id',
        ),
    );

    public function rules(){
        return array(
            'name' => array(
                array('not_empty'),
                array('min_length', array('value:',3)),
            ),        );
    }

    public function labels(){
        return array(
            'id' => __('Id'),
            'category_id' => 'Категория',
            'addtime' => __('Create Time'),
            'user_id' => __('User ID'),
            'name' => __('Company name'),
            'nameLink' => __('Company name'),
            'desc' => __('Company description'),
            'hits' => __('Hits'),
            'votes' => __('Votes'),
            'rating' => __('Rating'),
            'metakey' => __('Meta keywords'),
            'metadesc' => __('Meta description'),
            'address' => __('Address'),
            'city' => __('City'),
            'state' => __('Region'),
            'country' => __('Country'),
            'postcode' => __('Postcode'),
            'telephone' => __('Phone'),
            'fax' => __('Fax'),
            'email' => __('Email'),
            'website' => __('Website'),
            'lat' => __('Latitude'),
            'lng' => __('Longitude'),
            'zoom' => __('Zoom'),
            'enable' => __('Enable'),
            'vip' => __('Vip'),
            'views' => __('Views'),
            'comments' => __('Comments count'),
            'photos' => __('Photos'),
        );
    }

    public function filters(){
        return array(
            'name' => array(
                array('trim')
            ),
            'desc' => array(
                array('trim')
            ),
            'address' => array(
                array('trim')
            ),
            'website' => array(
                array(array($this,'finalizeSource'))
            ),
        );
    }

    /**
     * Добавить фото к объявлению
     * @param $file
     * @param $attributes
     * @return bool|ORM
     */
    public function addPhoto( $file, $attributes = array()){
        if(!$this->loaded() || !Image::isImage($file))
            return false;
        $photo = ORM::factory('CatalogCompanyPhoto')->values(array(
            'company_id'=>$this->pk(),
        ))->values($attributes)->save();
        $photo->savePhoto($file);
        $photo->saveThumb($file);
        $photo->savePreview($file);
        return $photo->update();
    }

    /**
     * Удалить фото
     * @param $id
     * @return bool
     */
    public function deletePhoto($id){
        $photo = ORM::factory('CatalogCompanyPhoto', $id);
        if($photo){
            $photo->delete();
            return true;
        }
        return false;
    }

    /**
     * Сохранение модели
     * @param Validation $validation
     * @return ORM|void
     */
    public function save(Validation $validation = NULL){
        if(!$this->addtime)
            $this->addtime = time();
        parent::save($validation);
    }

    /**
     * Удаление модели
     * @return ORM|void
     */
    public function delete(){
        foreach( $this->photos->find_all() as $photo)
            $photo->delete();
        if(is_dir(DOCROOT."/media/upload/catalog/". $this->id))
            rmdir(DOCROOT."/media/upload/catalog/". $this->id);
        parent::delete();
    }

    /**
     * @param null $id
     */
    public function setMainPhoto($id = NULL){
        $photo_table = ORM::factory('CatalogCompanyPhoto')->table_name();
        $main = ORM::factory('CatalogCompanyPhoto')->where('company_id' ,'=', $this->id)->and_where('main' ,'=', 1)->find();
        $exists = $main->loaded();
        if($id){
            DB::update($photo_table)->set(array('main'=>0))->where('company_id' ,'=', $this->id)->execute();
            $exists = DB::update($photo_table)->set(array('main'=>1))->where('company_id' ,'=', $this->id)->and_where('id' ,'=', $id)->execute();
        }
        if(!$exists){
            $photo = ORM::factory('CatalogCompanyPhoto')->where('company_id' ,'=', $this->id)->find();
            if($photo)
                DB::update($photo_table)->set(array('main'=>1))->where('company_id' ,'=', $this->id)->and_where('id' ,'=', $photo->id)->execute();
        }
    }

    /**
     * Getting article uri
     * @return string
     */
    public function getUri(){
        if(is_null($this->_uriToMe)){
            $categories = Model_CatalogCategory::getCategoriesList();
//            $parts_uris = array_flip(Model_NewsCategory::$parts_uri);

            $this->_uriToMe = Route::get('catalog_company')->uri(array(
                'id' => $this->id,
                'cat_alias' => $categories[$this->category_id]->alias,
//                'part_alias' => $parts_uris[$categories[$this->category_id]->part_id],
                'alias' => Text::transliterate($this->name, true),
            ));
        }
        return $this->_uriToMe;
    }

    /**
     * Finalize entered website before saving model
     * @param $website
     * @return string
     */
    public function finalizeSource($website){
        if(!empty($website) && !strstr($website, 'http://'))
            $website = 'http://'.$website;
        return $website;
    }

    /**
     * Return formated source link
     * @param array $parameters
     * @return null|string
     */
    public function getSourceLink(Array $parameters = array()){
        if(!empty($this->website)){
            $name = str_replace('http://','',$this->website);
            $name = str_replace('www.','',$name);
            $name = preg_replace('/\/.*/u','',$name);
            $parameters['target'] = '_blank';
            return HTML::anchor('/catalog/goto/' . $this->id, $name, $parameters);
        }
        return NULL;
    }

    /**
     * Return company full address
     * @return string
     */
    public function getCompanyAddress(){
        $address = '';
        if(!empty($this->country))
            $address .= $this->country;
        if(!empty($this->state))
            $address .= (!empty($address) ? ', ' : '') . $this->state;
        if(!empty($this->city))
            $address .= (!empty($address) ? ', ' : '') . $this->city;
        if(!empty($this->address))
            $address .= (!empty($address) ? ', ' : '') . $this->address;
        return $address;
    }

    /**
     * Flip company status
     */
    public function flipStatus(){
        $this->enable = $this->enable == 0 ? 1 : 0;
        $this->update();
    }

    /**
     * Redirection to source url
     */
    public function gotoSource(){
        if(!empty($this->website)){
            header("Location: ". $this->website);
        }
        else{
            header("Location: ". $this->getUri());
        }
        die();
    }

    /**
     * Smart model field getter
     * @param string $name
     * @return mixed|string
     */
    public function __get($name){
        if($name == 'nameLink'){
            return HTML::anchor($this->getUri(), $this->name, array('target'=>'_blank'));
        }
        return parent::__get($name);
    }

    /**
     * Request links array for sitemap generation
     * @return array
     */
    public function sitemapCompanies(){
        $links = array();
        $models = ORM::factory('CatalogCompany')->where('enable','=','1')->find_all();
        foreach($models as $model)
            $links[] = $model->getUri();
        return $links;
    }


    /**
     * Count comment objects array
     * than has not been moderated before
     * @return int
     */
    public function countNotModerated(){
        $count = ORM::factory($this->object_name())->where('moderate', '=', 0)->count_all();
        return $count;
    }
}