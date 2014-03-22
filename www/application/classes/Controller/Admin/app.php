<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_App extends Controller_Public_App {
    
    public function before() 
    {
        parent::before();
        if(Auth::instance()->logged_in('admin') == false)
        {
            $this->request->redirect('login');
        }
    }
}
