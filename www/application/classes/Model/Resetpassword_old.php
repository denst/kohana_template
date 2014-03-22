<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_ResetPassword extends ORM {
    
    protected $_table_name = 'reset_password';
    
    private $user_id;
    private $errors;

    public function write_temp_link($email, $temp_link)
    {
        try 
        {
            ORM::factory('resetpassword')
                ->set('email', $email)
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
        if(($reset_password = Helper_Values::get_value('resetpassword', false, 'temp_link', $temp_link)))
        {
            $reset_date_plus_1_hour = strtotime('+1 hours', strtotime($reset_password->date));
            if(time() > $reset_date_plus_1_hour)
            {
                $this->errors = 'This link has expired';
                return false;                
            }
            elseif($reset_password->status == 'used')
            {
                $this->errors = 'This link is already used';
                return false;
            }
            $reset_password->set('status', 'used')->update();
            $this->user_id = Model::factory('user')->get_user_by_field_value('email', 
                    $reset_password->email)->id;
            return true;
        }
        else
        {
            $this->errors = 'This link is incorrect';
            return false;
        }
    }
    
    public function get_user_id()
    {
        return $this->user_id;
    }
    
    public function get_errors()
    {
        return $this->errors;
    }
}
