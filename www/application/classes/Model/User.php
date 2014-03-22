<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_User extends Model_Auth_User {

    private $errors = array();

    protected $_table_name = 'users';
    
    protected $_has_many = array(
        'user_tokens' => array('model' => 'User_Token'),
        'roles'       => array('model' => 'Role', 'through' => 'roles_users'),
    );

    public function get_errors()
    {
        return $this->errors;
    }
    
    /**
     * Сreate a new user.
     *
     * @param   array   $fields
     * @return  mixed   user or false
     */
    public function register_user($fields) 
    {
        try 
        {
            $fields = Arr::map('strip_tags', $fields);
            $fields['email'] = trim(strtolower($fields['email']));
            $user = ORM::factory('User')->create_user($fields, array(
                'username',
                'password',
                'email',
            ));
            $user->add('roles', ORM::factory('Role', array('name' => 'login')));
            $this->set_old_password($user->id, $fields['password']);
            return $user;
        } 
        catch (ORM_Validation_Exception $e) 
        {
            $errors = $e->errors('register');
            $this->errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
            return false;
        }
    }
    
//    public function update_user($values, $expected = NULL, $is_admin = false)
//    {
//        $user = ORM::factory('user', $values['user_id']);
//        if($is_admin OR $this->check_old_password($user, $values))
//        {
//            $values = Helper_UrlPath::check_url($values);
//            if($this->update_fields_validation($values))
//            {
//                try 
//                {
//                    if (empty($values['password']))
//                        unset($values['password'], $values['password_confirm']);
//                    $extra_validation = Model_User::get_password_validation($values);
//
//                    $user->values($values, $expected)->update($extra_validation);
//
//                    if(isset($values['password']))
//                        $this->set_old_password($user->id, $values['password']);
//                    return true;
//                }
//                catch (ORM_Validation_Exception $exc) 
//                {
//                    $errors = $exc->errors('register');
//                    $this->errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
//                    $this->set_error_message();
//                    return false;
//                }
//            }
//            else
//                return false;
//        }
//        else
//            return false;
//    }
//    
//        
//    public function delete_user($publisher_id)
//    {
//        try 
//        {
//            $publisher = ORM::factory('user', $publisher_id);
//            $ad_zones = Model::factory('adzone')->get_zones_by_publisher_id($publisher_id);
//            foreach ($ad_zones as $zone) 
//            {
//                Model::factory('adzone')->delete_zone($zone->id);
//            }
//            FastSpring::unsubscribe($publisher_id);
//            $publisher->delete();
//            return true;
//        }
//        catch (ORM_Validation_Exception $exc) 
//        {
//            return false;
//        }
//    }
    

    
    /**
     * Set value of the old password to be able to change the password.
     *
     * @param     int      $user_id   
     * @param  string   $password
     * @return    bool     true
     */
    public function set_old_password($user_id, $password)
    {
        $user = $this->get_user_by_id($user_id);
        $user->set('old_password', Encrypt::instance()->encode($password))->update();
        return true;
    }
    
    public function get_user_by_id($id)
    {
        return Helper_Values::get_value('user', $id);
    }
    
    public function get_user_by_field_value($field, $value)
    {
        return Helper_Values::get_value('user', false, $field, $value);
    }
    
    /**
     * Function is related to Reset Password Module.
     *
     * @param     int      $user_id   
     * @param     string   $password
     * @return    bool     true or false
     */
    public function set_new_password($user_id, $password)
    {
        try
        {
            ORM::factory('User', $user_id)->set('password', $password)->update();
            $this->set_old_password($user_id, $password);
            return true;
        }
        catch(ORM_Validation_Exception $e)
        {
            return false;
        }
    }
    
    //====================== private functions ======================//

//    private function update_fields_validation($fields)
//    {
//        $validation = Validation::factory($fields);
//        $validation
//            ->rule('terms', 'url', array(':value'))
//            ->rule('host', 'Model_User::validate_domain_name', array(':value'));
//        if(!$validation->check())
//        {
//            $this->errors = $validation->errors('register');
//            $this->set_error_message();
//            return false;
//        }
//        return true;
//    }
    
    private function check_old_password($user, $post)
    {
        $errors = array();
        if($post['password'] != "" AND $post['password_confirm'] != '')
        {
            if($post['old_password'] == '')
                $errors['old_password'] = "Field old password can't be empty";
            elseif(Encrypt::instance()->decode($user->old_password) != $post['old_password'])
                $errors['old_password'] = 'Wrong old password';

            if(Valid::not_empty($errors))
            {
                $this->errors = $errors;
                return false;
            }
        }
        return true;
    }
}