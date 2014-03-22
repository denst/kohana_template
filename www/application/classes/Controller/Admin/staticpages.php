<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_StaticPages extends Controller_Admin_App {
    
    public function action_index() 
    {
        $this->template->content = View::factory('admin/staticpages/index')
            ->set('static_pages', Model::factory('staticpages')->get_all_pages());
    }
    
    public function action_add()
    {
        $this->template->content = View::factory('admin/staticpages/add_edit');
    }
    
    public function action_edit()
    {
        if (Valid::not_empty($_POST)) 
        {
            $data['static_page'] = Model::factory('staticpages')->get_page_by_id($_POST['page_id']);
            $this->template->content = View::factory('admin/staticpages/add_edit', $data);
        }
        else
            throw new HTTP_Exception_404;
    }
    
    public function action_delete() 
    {
        if (Valid::not_empty($_POST)) 
        {
            $model_static_pages = Model::factory('staticpages');
            if($model_static_pages->delete_page($_POST['static_page']))
                Helper_Message::add ('success', 'Static page is successfully removed');
            else
                Helper_Message::add ('error', $model_static_pages->get_errors());
            $this->request->redirect('admin/staticpages');
        }
        else
            throw new HTTP_Exception_404;
    }

    public function action_execution()
    {
        if(Valid::not_empty($_POST))
        {
            $model_static_pages = Model::factory('staticpages');
            switch ($_POST)
            {
                case isset($_POST['add']):
                    $model_static_pages->add_page($_POST['title'], $_POST['content']);
                    break;
                case isset($_POST['edit']):
                    if($model_static_pages->edit_page($_POST['title'], $_POST['content'], $_POST['id']))
                        Helper_Message::add ('success', 'Static page successfully modified');
                    break;
            }
            if(Valid::not_empty($model_static_pages->get_errors()))
                Helper_Message::add ('error', $model_static_pages->get_errors());
            $this->request->redirect('admin/staticpages');
        }
        else
            throw new HTTP_Exception_404;
    }
}