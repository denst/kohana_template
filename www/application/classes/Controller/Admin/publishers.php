<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Publishers extends Controller_Admin_App {
    
    public function action_index()
    {
        $data['plans'] = Model::factory('plans_plan')->get_all_plans();
        $data['publishers'] = Model::factory('user')->get_publishers();
        $this->template->content = View::factory('admin/publishers/index', $data);
    }
    
    public function action_changestatus()
    {
        if($this->request->is_ajax())
        {
            if(Valid::not_empty($_POST))
            {
                if(($status = Model::factory('user')->change_status($_POST)))
                    echo $status;
                else
                    echo false;
            }
            else
                echo false;
            exit();
        }
        else
            throw new HTTP_Exception_404;
    }
    
    public function action_edit() 
    {
        if (Valid::not_empty($_POST) AND isset($_POST['edit'])) 
        {
            if($_POST['submit'] == 'cancel')
                $this->request->redirect('admin/publishers');
            else
            {
                $model_user = Model::factory('user');
                $_POST['user_id'] = $_POST['publisher_id'];
                if($model_user->update_user($_POST, 
                        array('password', 'email', 'terms', 'scheme', 'host'), true))
                {
                    Helper_Message::add('success', 
                            'Information has been successfully updated');
                    $this->request->redirect('admin/publishers');
                }
                else
                {
                    $data = $_POST;
                    $data['errors'] = $model_user->get_errors();
                }
            }
        }
        $data['publisher'] = Model::factory('user')
            ->get_user_by_id($_POST['publisher_id']);
        $this->template->content = View::factory('admin/publishers/edit', $data);
    }
    
    public function action_sendemail()
    {
        if (Valid::not_empty($_POST)) 
        {
            switch ($_POST)
            {
                case isset($_POST['publisher']):
                    if(Model::factory('email')->send_mail_to_users($_POST['publisher_email'],
                            $_POST['subject'], $_POST['message']))
                        Helper_Message::add('success', 'Message was sent successfully');
                    break;
                case isset($_POST['all_publishers']):
                    $publishers_email = explode(',', $_POST['publishers_email']);
                    if(Model::factory('email')->send_mail_to_users($publishers_email,
                            $_POST['subject'], $_POST['message']))
                        Helper_Message::add('success', 'Message was sent successfully');
                    break;
            }
            $this->request->redirect('admin/publishers');
        }
        else
            throw new HTTP_Exception_404;
    }
    
    public function action_changeplan()
    {
        if (Valid::not_empty($_POST)) 
        {
            if(Model::factory('plans_users')->change_plans(
                Model::factory('user')->get_user_by_id($_POST['publisher_id']),
                    Model::factory('plans_plan')->get_plan_by_id($_POST['plan'])))
                Helper_Message::add ('success', 'Tariff plan was successfully changed');
            $this->request->redirect('admin/publishers');
        }
        else
            throw new HTTP_Exception_404;
    }
    
    public function action_deleteaccount() 
    {
        if (Valid::not_empty($_POST)) 
        {
            Model::factory('user')->delete_account($_POST['publisher_id']);
            Helper_Message::add ('success', 'Publisher account was successfully deleted');
            $this->request->redirect('admin/publishers');
        }
        else
            throw new HTTP_Exception_404;
    }
}
