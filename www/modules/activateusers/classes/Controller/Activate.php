<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Activate extends Controller_Template {
    
    public $template = 'activate/index';
    
    public function before()
    {
        if ($this->auto_render)
        {
            $this->template = View::factory($this->template);
            $this->template->content = '';
        }
    }

    public function after()
    {
        if ($this->auto_render === TRUE)
        {
            $this->response->body($this->template);
        }
        parent::after();
    }
    
    public function action_activate() 
    {
        $link = $this->request->param('id');
        if($link != '')
        {
            $model_user = Model::factory('user');
            if($model_user->check_link($link))
            {
                $this->request->redirect('publisher/'.
                        Auth::instance()->get_user()->username);
            }
            else
            {
                Helper_Message::add('error', $model_user->get_errors());
                $this->request->redirect('login');
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