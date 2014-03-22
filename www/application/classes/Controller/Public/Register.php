<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Public_Register extends Controller_Public_App {
    
    public function action_signup()
    {
        $this->page_title('signup');
        $view = View::factory('registration/signup');
        
        if(Valid::not_empty($_POST))
        {
            $model_users = Model::factory('User');
            
            if(($user = $model_users->register_user($_POST)))
            {
                Helper_Message::add('success', 
                    'Thank you for registration');
                $this->redirect('login');
            }
            else
            {
                $view->set('errors', $model_users->get_errors());
                $view->set('post', $_POST);
            }
        }
        
        $this->template->content = $view;
    }
    
    public function action_login() 
    {
        $this->page_title('login');
        $view = View::factory('registration/login');
        if(Valid::not_empty($_POST))
        {
            $remember = isset($_POST['remember']) && $_POST['remember'];
            if (Auth::instance()->login($_POST['email'], $_POST['password'], $remember))
                $this->redirect('user/profile');
            else
            {
                 Helper_Message::add('error', 'Email or Password is incorrect.');
                 $view->set('post', $_POST);
            }
        }
        
        $this->template->content = $view;
    }

    public function action_logout()
    {
        Auth::instance()->logout();
        $this->request->redirect('home');
    }
}
