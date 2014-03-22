<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Email_ResetPassword {
    
    public function send($user, $temp_link)
    {
        $link_for_reset_password = URL::base('http').'resetpassword/newpassword/'.$temp_link;
        $subject = 'Reset password for '.URL::base('http');
        $view = View::factory('resetpassword/email')
            ->set('user', $user)
            ->set('link_for_reset_password', $link_for_reset_password);
        
        if(Model::factory('Email')->send($user->email, $subject, $view->render()))
            return true;
        else
            return false; 
            
    }
}