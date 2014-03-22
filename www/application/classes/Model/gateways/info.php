<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Gateways_Info extends ORM {
    
    protected $_table_name = 'gateways_info';
    
    private $encrypt;
    
    public function set_info($post)
    {
        try 
        {
            $this->encrypt = Encrypt::instance();
            $info = $this->get_gateways_info($post['user_id']);
            $info->values($post);
            
            if(Valid::not_empty($post['paypal_email']))
                $info->set('paypal_enable', 1);
            else
                $info->set('paypal_enable', 0);
            
            if(Valid::not_empty($post['authorizenet_api_login_id']) AND
                    Valid::not_empty($post['authorizenet_api_login_key']))
            {
                $info->set('authorizenet_enable', 1);
                $info->set('authorizenet_api_login_id', 
                        $this->encrypt->encode($post['authorizenet_api_login_id']));
                $info->set('authorizenet_api_login_key', 
                        $this->encrypt->encode($post['authorizenet_api_login_key']));
            }
            else
                $info->set('authorizenet_enable', 0);
            
            $info->update();
            
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
    
    public function get_gateways_info($user_id)
    {
        $info = Helper_Values::get_value('gateways_info', false, 'user_id', $user_id);
        if(! $info)
            $info = ORM::factory('gateways_info')
                ->set('user_id', $user_id)
                ->create();
        return $info;
    }
    
    public function set_total_income($user, $amount)
    {
        try 
        {
            $gateways_info = $user->gatewaysinfo;
            $total_amount = $gateways_info->total_income + $amount;
            $gateways_info->set('total_income', $total_amount)->update();
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
                
    }
}