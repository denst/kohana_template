<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_ResetPassword extends ORM {
    
    protected $_table_name = 'reset_password';
    
    private $user_id;
    private $errors;
    
    public function email_exist($email)
    {
        if($email == Kohana::$config->load('resetpassword.admin_email'))
            $email = "";
        $res = Helper_Values::get_value('User', false, 'email', $email);
        if($res)
            return true;
        else
            return false;
    }
    
    public function check_email($email)
    {
        $validation = Validation::factory(array('email' => $email));
        $validation
            ->rule('email', 'not_empty')
            ->rule('email', 'email')
            ->rule('email', array($this, 'email_exist'), array(':value'));
        if($validation->check())
            return Helper_Values::get_value('User', false, 'email', $email);
        else 
        {
            $this->errors = $validation->errors('resetpassword');
            return false;
        }
    }

    public function write_temp_link($user, $temp_link)
    {
        try 
        {
            ORM::factory('resetpassword')
                ->set('user_id', $user->id)
                ->set('email', $user->email)
                ->set('temp_link', $temp_link)
                ->set('status', 'send')
                ->set('date', date("Y-m-d H:m:s"))
                ->save();
           return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
    
    public function check_link($temp_link)
    {
        if(($reset_password = Helper_Values::get_value('Resetpassword', false, 'temp_link', $temp_link)))
        {
            $reset_date_plus_1_hour = strtotime('+1 hours', strtotime($reset_password->date));
            $temp = time();
            $temp_bool = time() > $reset_date_plus_1_hour;
            switch ($reset_password) {
                case time() > $reset_date_plus_1_hour:
                    $this->errors = 'This link has expired';
                    break;
                case $reset_password->status == 'used':
                    $this->errors = 'This link has expired';
                    break;
                default:
                    $reset_password->set('status', 'used')->update();
                    $this->user_id = Helper_Values::get_value('User', false, 'email', 
                    $reset_password->email)->id;
                    return true;
            }
        }
        else
        {
            $this->errors = 'This link is incorrect';
        }
        return false;
    }
//    public function check_link($temp_link)
//    {
//        if(($reset_password = Helper_Values::get_value('resetpassword', false, 'temp_link', $temp_link)))
//        {
//            $reset_date_plus_1_hour = strtotime('+1 hours', strtotime($reset_password->date));
//            if(time() > $reset_date_plus_1_hour)
//            {
//                $this->errors = 'This link has expired';
//                return false;                
//            }
//            elseif($reset_password->status == 'used')
//            {
//                $this->errors = 'This link is already used';
//                return false;
//            }
//            $reset_password->set('status', 'used')->update();
//            $this->user_id = Helper_Values::get_value('User', false, 'email', 
//                    $reset_password->email)->id;
//            return true;
//        }
//        else
//        {
//            $this->errors = 'This link is incorrect';
//            return false;
//        }
//    }

    public function get_user_id()
    {
        return $this->user_id;
    }
    
    public function get_errors()
    {
        return $this->errors;
    }
}