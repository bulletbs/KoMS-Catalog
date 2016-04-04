<?php defined('SYSPATH') or die('No direct script access.');
/**
 *
 */

class Controller_UserCompany extends Controller_User
{
    public $auth_required = 'login';

    public $skip_auto_content_apply = array(
        'enable',
        'remove',
    );

    public function before(){
        /* Путь к шаблону */

        $this->uri = 'catalog/cabinet/'. $this->request->action();
        parent::before();
    }


    /**
     * Companies list action
     */
    public function action_list(){
        $companies = ORM::factory('CatalogCompany')->where('user_id', '=', $this->current_user->id)->find_all();
        $this->template->content->set(array(
            'companies' => $companies,
        ));
    }

    /**
     * Company add & edit action
     */
    public function action_edit(){
        $errors = array();
        $id = $this->request->param('id');

        $this->breadcrumbs->add(__('My companies'), URL::site().Route::get('catalog_mycompany')->uri());

        $model = ORM::factory('CatalogCompany')->where('id', '=', $id)->and_where('user_id', '=', $this->current_user->id)->find();
        $photos = $model->photos->find_all();
        if($id > 0 && !$model->loaded())
            $this->redirect(URL::site().Route::get('catalog_mycompany')->uri());

        if(HTTP_Request::POST == $this->request->method()){
            if(Arr::get($_POST, 'cancel'))
                $this->redirect(URL::site().Route::get('catalog_mycompany')->uri());

            $model->values($_POST);
            $model->user_id = $this->current_user->id;
            try{
                $model->save();

                /* Save photos */
                $files = Arr::get($_FILES, 'photos', array('tmp_name' => array()));
                foreach ($files['tmp_name'] as $k => $file) {
                    $model->addPhoto($file);
                }

                /* Deleting photos */
                $files = Arr::get($_POST, 'delphotos', array());
                foreach ($files as $file_id)
                    $model->deletePhoto($file_id);

                /* Setting up main photo */
                $setmain = Arr::get($_POST, 'setmain');
                $model->setMainPhoto($setmain);

                Flash::success(__('Your company successfully saved'));
                $this->redirect(URL::site().Route::get('catalog_mycompany')->uri());
            }
            catch(ORM_Validation_Exception $e){
                $errors = $e->errors('validation');
            }
        }

        $this->template->content->set(array(
            'model' => $model,
            'photos' => $photos,
            'errors' => $errors,
            'categories' => ORM::factory('CatalogCategory')->getOptionList(),
        ));
    }

    /**
     * Company enable/disable action
     */
    public function action_enable(){
        $id = $this->request->param('id');
        $model = ORM::factory('CatalogCompany')->where('id', '=', $id)->and_where('user_id', '=', $this->current_user->id)->find();
        if($id > 0 && !$model->loaded()){
            $this->redirect(URL::site().Route::get('catalog_mycompany')->uri());
            Flash::warning(__('Company not found'));
        }
        else{
            Flash::success(__('Your company successfully turned '. ($model->enable ? 'off' : 'on')));
            $model->flipStatus();
            $this->redirect(URL::site().Route::get('catalog_mycompany')->uri());

        }
    }

    /**
     * Company remove action
     */
    public function action_remove(){
        $id = $this->request->param('id');
        $model = ORM::factory('CatalogCompany')->where('id', '=', $id)->and_where('user_id', '=', $this->current_user->id)->find();
        if($id > 0 && !$model->loaded()){
            $this->redirect(URL::site().Route::get('catalog_mycompany')->uri());
            Flash::warning(__('Company not found'));
        }
        else{
            $model->delete();
            Flash::success(__('Your company successfully removed'));
            $this->redirect(URL::site().Route::get('catalog_mycompany')->uri());

        }

    }
}