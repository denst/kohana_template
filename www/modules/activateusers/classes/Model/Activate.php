<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Activate extends ORM {
    
    protected $_table_name = 'template';
    
    protected $_belongs_to = array(
        '' => array('model' => ''),
    );
    
    public function check_link($temp_link)
    {
        if(($user = Helper_Values::get_value('user', false, 'activate_link', $temp_link)))
        {
            if($user->link_status == 'used')
            {
                $this->errors = 'This link is already used';
                return false;
            }
            $user
                ->set('first_login', Helper_Date::get_current_mkdate())
                ->set('link_status', 'used')
                ->set('account_status', 1)
                ->update();
            $this->admin_activate_publisher_account($user);
            Auth::instance()->login($user->email, 
                    Encrypt::instance()->decode($user->old_password));
            return true;
        }
        else
        {
            $this->errors = 'This link is incorrect';
            return false;
        }
    }
    
    public function resend_activation_email($user_id)
    {
        $user = ORM::factory('user', $user_id);
        if(Model::factory('email')->activation_email($user->email, $user->activate_link))
            return true;
        else
            return false;
        
    }
}