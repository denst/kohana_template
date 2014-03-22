<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ResetPassword extends Controller_Public_App {
    
    public function action_index() 
    {
        $this->page_title('reset password');
        $view = View::factory('resetpassword/forgot');
        
        if(Valid::not_empty($_POST))
        {
            $model_reset_password = Model::factory('Resetpassword');
            
            if(($user = $model_reset_password->check_email($_POST['email'])))
            {
                $temp_link = Text::random('alnum', 50);
                $model_reset_password->write_temp_link($user, $temp_link);
                if(Model::factory('Email_Resetpassword')->send($user, $temp_link))
                {
                    Helper_Message::add('success', 'Message has been sent successfully');
                    $this->redirect('login');
                }
                else
                    Helper_Message::add('error', 'Message was not sent');
            }
            else
            {
                $view->set('errors', $model_reset_password->get_errors());
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
            $model_reset_password = Model::factory('Resetpassword');
            if($model_reset_password->check_link($link))
            {
                Session::instance()->set('user_id', $model_reset_password->get_user_id());
                $this->template->content = View::factory('resetpassword/newpassword');
            }
            else
            {
                Helper_Message::add('error', $model_reset_password->get_errors());
                $this->redirect('login');
            }
        }
        else
            throw new HTTP_Exception_404;
    }
    
    public function action_setnewpassword()
    {
        if(Valid::not_empty($_POST))
        {
            if($_POST['password'] === $_POST['password_confirm'] AND $_POST['password'] != '')
            {
                if(Model::factory('User')->set_new_password(
                    Session::instance()->get_once('user_id'), $_POST['password']))
                {
                    Helper_Message::add('success', 'The password was successfully changed');
                    $this->redirect('login');
                }
            }
            else
            {
                Helper_Message::add('error', 'The password and password confirmation are different');
                $this->template->content = View::factory('resetpassword/newpassword');
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
