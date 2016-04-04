<?php

class Model_CatalogCompanyPhoto extends ORM{

    CONST THUMB_WIDTH = 100;
    CONST THUMB_HEIGHT = 100;

    CONST PREVIEW_WIDTH = 250;
    CONST PREVIEW_HEIGHT = 200;

    CONST PHOTO_WIDTH = 800;
    CONST PHOTO_HEIGHT = 800;


    protected $_table_name = 'catalog_company_photo';

	protected $_belongs_to = array(
        'news'=> array(
            'model' => 'CatalogCompany',
            'foreign_key' => 'company_id',
        ),
    );

    public function labels(){
        return array(
            'id'        => __('Id'),
            'news_id'   => __('NewId'),
            'width'     => __('Width'),
            'height'    => __('Height'),
            'ext'       => __('Extension'),
        );
    }

    public function delete(){
        if($this->getPhoto())
            unlink($this->getPhoto());
        if($this->getThumb())
            unlink($this->getThumb());
        if($this->getPreview())
            unlink($this->getPreview());
        parent::delete();
    }

    public function savePhoto($file){
        if(!$this->loaded() || !is_file($file))
            return;
        $image = Image::factory($file);
        if(!$this->ext)
            $this->ext = $image->findExtension();
        $image->image_set_max_edges(self::PHOTO_WIDTH);
        $this->width = $image->width;
        $this->height = $image->height;
        $image->save($this->getPhoto(true));
    }

    public function saveThumb($file, Array $coords = array()){
        if(!$this->loaded() || !is_file($file))
            return;
        $image = Image::factory($file);
        if(count($coords) && isset($coords['thumb_w'], $coords['thumb_h'], $coords['thumb_x'], $coords['thumb_y']) ){
            $image->crop($coords['thumb_w'], $coords['thumb_h'], $coords['thumb_x'], $coords['thumb_y']);
            $image->resize(self::THUMB_WIDTH);
        }
        else{
//            $image->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT, Image::INVERSE);
//            $image->crop(self::THUMB_WIDTH, self::THUMB_HEIGHT, NULL, 0);
            $image->smart_resize(self::THUMB_WIDTH, self::THUMB_HEIGHT);
        }
        $image->save($this->getThumb(true));
    }

    public function savePreview($file, Array $coords = array()){
        if(!$this->loaded() || !is_file($file))
            return;
        $image = Image::factory($file);
        if(count($coords) && isset($coords['prev_w'], $coords['prev_h'], $coords['prev_x'], $coords['prev_y']) ){
            $image->crop($coords['prev_w'], $coords['prev_h'], $coords['prev_x'], $coords['prev_y']);
            $image->resize(self::PREVIEW_WIDTH);
        }
        else{
//            $image->resize(self::PREVIEW_WIDTH);
//            $image->crop(self::PREVIEW_WIDTH, self::PREVIEW_HEIGHT, NULL, 0);
            $image->smart_resize(self::PREVIEW_WIDTH, self::PREVIEW_HEIGHT);
        }
        $image->save($this->getPreview(true));
    }

    public function getPhoto($getName = false){
        if($getName===TRUE || is_file($this->getPhotoPath() . $this->id .'.'.$this->ext))
            return $this->getPhotoPath() . $this->id .'.'.$this->ext;
        return;
    }
    public function getThumb($getName = false){
        if($getName===TRUE || is_file($this->getThumbPath() . $this->id .'_thumb.'.$this->ext))
            return $this->getThumbPath() . $this->id .'_thumb.'.$this->ext;
        return;
    }
    public function getPreview($getName = false){
        if($getName===TRUE || is_file($this->getPreviewPath() . $this->id .'_prev.'.$this->ext))
            return $this->getPreviewPath() . $this->id .'_prev.'.$this->ext;
        return;
    }

    public function getPhotoPath(){
        if(!file_exists(DOCROOT."/media/upload/catalog/". $this->company_id ."/"))
            mkdir(DOCROOT."/media/upload/catalog/". $this->company_id);
        return DOCROOT . "/media/upload/catalog/". $this->company_id ."/";
    }

    public function getThumbPath(){
        return $this->getPhotoPath();
    }


    public function getPreviewPath(){
        return $this->getPhotoPath();
    }

    public function getPhotoUri(){
        if(is_file($this->getThumbPath() . $this->id .'.'.$this->ext))
            return Kohana::$base_url."/media/upload/catalog/". $this->company_id . "/" . $this->id . '.' . $this->ext;
        return NULL;
    }

    public function getThumbUri(){
        if(is_file($this->getThumbPath() . $this->id .'_thumb.'.$this->ext))
            return Kohana::$base_url."/media/upload/catalog/". $this->company_id . "/" . $this->id . '_thumb.' . $this->ext;
        return NULL;
    }

    public function getPreviewUri(){
        if(is_file($this->getPreviewPath() . $this->id .'_prev.'.$this->ext))
            return Kohana::$base_url."/media/upload/catalog/". $this->company_id . "/" . $this->id . '_prev.' . $this->ext;
        return NULL;
    }

    public function getPhotoTag($alt = '', Array $attributes = array()){
        $photo = $this->getPhotoUri();
        if($photo)
            return HTML::image($photo, Arr::merge(array(
                'alt'=>$alt,
                'title'=>$alt,
            ), $attributes));
        return NULL;
    }

    public function getPreviewTag($alt='', Array $attributes = array()){
        $photo = $this->getPreviewUri();
        if($photo)
            return HTML::image($photo, Arr::merge(array(
                'alt'=>$alt,
                'title'=>$alt,
            ), $attributes));
        return NULL;
    }

    public function getThumbTag($alt='', Array $attributes = array()){
        $photo = $this->getThumbUri();
        if($photo)
            return HTML::image($photo, Arr::merge(array(
                'alt'=>$alt,
                'title'=>$alt,
            ), $attributes));
        return NULL;
    }

    /**
     * Find list of photos by requested articles ids
     * @param array $ids
     * @return array|object
     */
    public function companiesPhotoList(Array $ids){
        $photos = array();
        if(count($ids)){
            $db_photos = DB::select()
                ->distinct('company_id')
                ->from($this->_table_name)
                ->where('company_id', 'IN', $ids)
                ->and_where('main', '=', 1)
                ->as_object('Model_CatalogCompanyPhoto')
                ->execute();
            ;
            foreach($db_photos as $photo)
                $photos[$photo->company_id] = $photo;
        }
        return $photos;
    }
}