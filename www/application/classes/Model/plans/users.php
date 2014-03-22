<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Plans_Users extends ORM {
    
    protected $_belongs_to = array(
        'plan' => array('model' => 'plans_plan', 'foreign_key' => 'plan_id',)
    );
    
    protected $_table_name = 'plans_users';

    public function change_plans($user, $plan)
    {
        $user->userplan
            ->set('plan_id', $plan->id)
            ->set('impressions', 0)
            ->set('warning_letter', 0)
            ->set('date_5_days', null)
            ->set('warning_letter_5_days', null)
            ->update();
        $user->set('account_status', 1)->update();
        $this->unblocked_publisher_ads($user);
        return true;
    }
    
    public function increase_tafiff_impression($ad_id)
    {
        $ad = Model::factory('ad')->get_ad_by_id($ad_id);
        $publisher= $ad->ad_zone->publisher;
        if($publisher->userplan->plan_id != 1)
        {
            try 
            {
                if(is_null($publisher->userplan->warning_letter_5_days))
                {
                    $user_plan = $publisher->userplan;
                    $total_impression = $user_plan->impressions + 1;
                    $user_plan->set('impressions', $total_impression)->update();
                    $this->check_boundary_conditions($publisher, $total_impression, $ad->ad_zone);
                }
                return true;
            } 
            catch (ORM_Validation_Exception $exc) 
            {
                return false;
            }
        }
    }
    
    private function check_boundary_conditions($user, $total_impressions, $ad_zone)
    {
        $user_plan = $user->userplan;
        $plan = $user_plan->plan;
        try 
        {
            $persent = round(($total_impressions * 100) / $plan->impressions);
            if($persent >= 80 AND $user_plan->warning_letter == 0)
            {
                $user_plan->set('warning_letter', 1)->update();
                Model::factory('email')->warrning_email($user->email);
            }
            if($total_impressions >= $plan->impressions)
            {
                Model::factory('user')
                    ->change_status(array('status' => $user->id.'&2'));
                Model::factory('email')->blocked_tariff_email($user->email);
                $this->set_5_days_trial_period($user);
            }
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
    
    public function set_5_days_trial_period($user, $set_days = null)
    {
        try        
        {
            $userplan = $user->userplan;
            if(empty($set_days))
            {
                $userplan->set('date_5_days', Helper_Date::get_current_mkdate());
                $set_days = 5;
            }
            $userplan
               ->set('warning_letter_5_days', $set_days)
               ->update();
            Model::factory('email')->warrning_5_days_email($user->email, $set_days);
            return true;
        }         
        catch (ORM_Validation_Exception $ex)         
        {
            return false;
        }
    }
    
    public function check_5_days_trial_period()
    {
        $publishers = ORM::factory('user')->find_all();
        foreach ($publishers as $publisher) 
        {
            if($publisher->has('roles', 3))
            {
                if(Valid::not_empty($publisher->userplan->date_5_days))
                {
                    $result = Date::span($publisher->userplan->date_5_days, 
                        Helper_Date::get_current_mkdate(), 'days');
                    if($result < 5 OR $result == 5)
                    {
                        if($result == 5 AND $publisher->userplan->warning_letter_5_days != 0)
                        {
                            $publisher->userplan->set('warning_letter_5_days', 0)->update();
                            $this->blocked_publisher_ads($publisher);
                        }
                        else
                            $this->set_5_days_trial_period($publisher, 5 - $result);
                    }
                }
            }
        }
    }
    
    public function blocked_publisher_ads($publisher)
    {
        $ads = Model::factory('ad')->get_ads_by_publisher_id($publisher->id);
        foreach ($ads as $ad) 
        {
            if($ad->status == 1 OR $ad->status == 2 OR $ad->status == 6)
                Model::factory('ad')->changestatus(array("status" => $ad->id.'&3'));
        }
        Model::factory('email')->blocked_ads_email($publisher->email);
    }
    
    private function unblocked_publisher_ads($publisher)
    {
        $ads = Model::factory('ad')->get_ads_by_publisher_id($publisher->id);
        foreach ($ads as $ad) 
        {
            if($ad->status == 3)
                Model::factory('ad')->changestatus(array("status" => $ad->id.'&1'));
        }
    }

    public function get_rest_days($user)
    {
        $result = Date::span($user->first_login, 
                Helper_Date::get_current_mkdate(), 'days');
        if($result >= 30)
            return $result = 30;
        return $result;
    }
    
    public function check_trial_period($user)
    {
        if($user->account_status == 1)
        {
            $rest_days = $this->get_rest_days($user);
            if($rest_days >= 30)
            {
                Model::factory('user')
                    ->change_status(array('status' => $user->id.'&2'));
                Model::factory('email')->blocked_trial_email($user->email);
                $this->set_5_days_trial_period($user);
            }
        }
        return true;
    }
    
    public function check_tariff_period($user)
    {
        $rest_days = Date::span(strtotime($user->userplan->date), 
            Helper_Date::get_current_mkdate(), 'days');
        if($rest_days >= 30)
            $this->clear_plan ($user);
        return true;
    }
    
    public function check_publishers_tariff()
    {
        $publishers = ORM::factory('user')->find_all();
        foreach ($publishers as $publisher) 
        {
            if($publisher->has('roles', 3))
            {
                if($publisher->userplan->loaded() AND $publisher->userplan->plan_id != 1)
                    $this->check_tariff_period($publisher);
                else
                    $this->check_trial_period ($publisher);
            }
        }
    }
    
    public function set_new_plan_end_date($user_id)
    {
        $plan = ORM::factory('plans_users')->where('user_id', '=', $user_id)->find();
        if($plan->loaded())
        {
            try         
            {
                $plan->set('date', date("Y-m-d H:m:s"))->update();
                return true;
            }
            catch (ORM_Validation_Exception $ex)
            {
                return false;
            }
        }
    }

    private function clear_plan($user)
    {
        try 
        {
            $user->userplan 
                ->set('impressions', 0)
                ->set('warning_letter', 0)
                ->set('date_5_days', null)
                ->set('warning_letter_5_days', null)
                ->set('date', date("Y-m-d H:m:s"))
                ->update();
            $user->set('account_status', 1)->update();
            $this->unblocked_publisher_ads($user);
            return true;
        } 
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }

    public function create_plan($user, $plan_id)
    {
        try 
        {
            ORM::factory('plans_users')
                ->set('user_id', $user->id)
                ->set('plan_id', $plan_id)
                ->set('impressions', 0)
                ->set('date', date("Y-m-d H:m:s"))
                ->create();
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
}
