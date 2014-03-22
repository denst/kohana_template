<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Public_ResetPassword extends Controller_Public_App {
    
    public function action_index() 
    {
        $this->page_title('reset password');
        $view = View::factory('registration/forgot');
        if(Valid::not_empty($_POST))
        {
            $model_users = Model::factory('User');
            if($model_users->check_email($_POST['email']))
            {
                $temp_link = Text::random('alnum', 50);
                Model::factory('Resetpassword')->write_temp_link($_POST['email'], $temp_link);
                if(Model::factory('Email')->reset_password($_POST['email'], $temp_link))
                {
                    Helper_Message::add('success', 'Message has been sent successfully');
                    $this->request->redirect('login');
                }
                else
                    Helper_Message::add('error', 'Message was not sent');
            }
            else
            {
                $view->set('errors', $model_users->get_errors());
                $view->set('post', $_POST);
            }
        }
        $this->template->content = $view;
    }
    
    public function action_newpassword()
    {
        $link = $this->request->param('id');
        if($link != '')
        {
            $model_resetpassword = Model::factory('resetpassword');
            if($model_resetpassword->check_link($link))
            {
                Session::instance()->set('user_id', $model_resetpassword->get_user_id());
                $this->template->content = View::factory('registration/newpassword');
            }
            else
            {
                Helper_Message::add('error', $model_resetpassword->get_errors());
                $this->request->redirect('login');
            }
        }
        else
            throw new HTTP_Exception_404;
    }
    
    public function action_setnewpassword()
    {
        if(isset($_POST['submit']))
        {
            if($_POST['password'] === $_POST['password_confirm'] AND $_POST['password'] != '')
            {
                if(Model::factory('user')->set_new_password(
                    Session::instance()->get_once('user_id'), $_POST['password']))
                {
                    Helper_Message::add('success', 'The password was successfully changed');
                    $this->request->redirect('login');
                }
            }
            else
            {
                $data['errors'] = true;
                Helper_Message::add('error', 'The password and password confirmation are different');
                $this->template->content = View::factory('registration/newpassword', $data);
            }
        }
        else
            throw new HTTP_Exception_404;
    }
    
    public function action_resend() 
    {
        if (Valid::not_empty($_POST)) 
        {
            if(Model::factory('user')->resend_activation_email($_POST['user_id']))
            Helper_Message::add('success', 
                    'Activation mail has been successfully resend');
            Session::instance()->set('resend', $_POST['user_id']);
            $this->request->redirect('login');
        }
        else
            throw new HTTP_Exception_404;
    }
}
