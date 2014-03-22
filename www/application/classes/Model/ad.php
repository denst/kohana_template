<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Ad extends ORM {
    
    private $errors = array();
    
    protected $_belongs_to = array(
        'ad_zone' => array('model' => 'adzone'),
        'advertiser'     => array(
             'model' => 'user',
             'foreign_key' => 'advertiser_id',
        )
    );
    

    public function create_ad($post, $bannner, $publisher_test = false)
    {
        if($publisher_test OR $this->fields_validation($post))
        {
            try 
            {
                $post  = Helper_UrlPath::check_url($post);
                if( !isset($post['paid']))
                    $post['paid'] = 0;
                if($publisher_test)
                    $post['first_date'] = -1;
                if(in_array('ALL', $post['target_country']))
                        $post['target_country'] = array($post['target_country'][0]);
                $post['target_country'] = json_encode($post['target_country']);
                $post['date'] = date('Y-m-d H:m:s');
                $post['banner_path'] = $bannner;
                $ad = ORM::factory('ad')
                    ->values($post)
                    ->create();
                Model::factory('user')->check_is_advertiser_user($post['advertiser_id']);
                return $ad;
            } 
            catch (ORM_Validation_Exception $exc) 
            {
                $this->errors = $exc->errors('register');
                return false;
            }
        }
        else
            return false;
    }
    
    public function edit_ad($post, $bannner)
    {
        try 
        {
            $post  = Helper_UrlPath::check_url($post);
            if(in_array('ALL', $post['target_country']))
                    $post['target_country'] = array($post['target_country'][0]);
            $post['target_country'] = json_encode($post['target_country']);
            $post['banner_path'] = $bannner;
            $ad = ORM::factory('ad', $post['ad_id']);
            if($ad->host != $post['host'] OR $ad->banner_path != $post['banner_path'])
            {
                if($ad->ad_zone->publisher->gatewaysinfo->ad_accept == 0)
                    $post['status'] = 0;
            }
            $ad->values($post)
                ->update();
            return true;
        } 
        catch (ORM_Validation_Exception $exc) 
        {
            $this->errors = $exc->errors('register');
            return false;
        }
    }
    
    public function delete_ad($ad)
    {
        try 
        {
            Model::factory('image')->delete_banner($ad->banner_path);
            $ad->delete();
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }

    public function set_banner_path($ad_id, $path)
    {
        try 
        {
            ORM::factory('ad', $ad_id)
                ->set('banner_path', $path)
                ->save();
            return true;
        } 
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }

    public function get_ads_by_publisher_id($user_id)
    {
        $ads = Db::select('ads.*', 'ad_zones.size',
                array('ad_zones.name', 'zone_name')
            )
            ->from('ads')
            ->join('ad_zones')->on('ad_zones.id', '=', 'ads.ad_zone_id')
            ->where('ad_zones.publisher_id', '=', $user_id)
            ->where('ads.first_date', '!=', 0)
            ->order_by('ads.id', 'DESC')
            ->as_object()
            ->execute();
        return $ads;
    }
    
    public function get_ads_by_ad_zone_id($ad_zone_id)
    {
        $ads = ORM::factory('ad')
                ->where('ad_zone_id', '=', $ad_zone_id)
                ->where('status', '=', '1')
                ->find_all();
        return $ads;
    }
    
    public function get_ads_by_advertiser_id($advertiser_id)
    {
        $ads = ORM::factory('ad')
                ->where('advertiser_id', '=', $advertiser_id)
                ->where('first_date', '!=', 0)
                ->order_by('id', 'DESC')
                ->find_all();
        if(count($ads) > 0)
            return $ads;
        else
            return false;
    }
    
    public function get_ad_by_id($ad_id)
    {
        return Helper_Values::get_value('ad', $ad_id);
    }
    
    public function get_ads_count_by_adzone_id($adzone_id)
    {
        $ad_count = DB::select('ads.ad_zone_id')
            ->from('ads')
            ->where('ads.ad_zone_id', '=', $adzone_id)
            ->where('ads.first_date', '!=', 0)
            ->where('ads.status', '=', 1)
            ->execute()
            ->count();
        return $ad_count;
    }

    public function changestatus($post)
    {
        try 
        {
            list($id, $status) = explode('&', $post['status']);
            $ad = ORM::factory('ad', $id);
            $ad->set('status', $status);
            $ad->update();
            Model::factory('email')->status_ad_email($ad->ad_zone->publisher,
                    $ad->advertiser, $this->get_status_name($status));
            return $status;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            ProfilerToolbar::add_log('error', $exc->getMessage(), true);
            return false;
        }
    }
    
    public function set_display($ad_id)
    {
        try 
        {
            ORM::factory('ad', $ad_id)
                ->set('show', 1)
                ->update();
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
    
    public function clean_display($ad_id)
    {
        try 
        {
            ORM::factory('ad', $ad_id)
                ->set('show', 0)
                ->update();
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
    
    public function increase_impression($ad_id)
    {
        try 
        {
            Model::factory('addatefilter')->increase_impression($ad_id);
            $ad = ORM::factory('ad', $ad_id);
            $impressions = $ad->total_impressions + 1;
            $ad->set('total_impressions', $impressions)->update();
            Model::factory('adzone')->check_adzone_tariff($ad, 'impressions');
            Model::factory('plans_users')->increase_tafiff_impression($ad_id);
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
    
    public function increase_click($ad_id)
    {
        try 
        {
            Model::factory('addatefilter')->increase_click($ad_id);
            $ad = ORM::factory('ad', $ad_id);
            $clicks = $ad->total_clicks + 1;
            $ad->set('total_clicks', $clicks)->update();
            Model::factory('adzone')->check_adzone_tariff($ad, 'clicks');
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }

    public function is_advertiser_ad($advertiser_name, $ad_id)
    {
        $array_ads = DB::select('ads.id')
            ->from('ads')
            ->join('users')->on('users.id', '=', 'ads.advertiser_id')
            ->where('users.username', '=', $advertiser_name)
            ->execute()
            ->as_array();
        foreach ($array_ads as $key => $value)
        {
            if($value['id'] == $ad_id)
                return true;
        }
        return false;
    }
    
    public function get_all_ads()
    {
        $ads = ORM::factory('ad')->order_by('id', 'DESC')->find_all();
        return $ads;
    }

    public function get_errors()
    {
        return $this->errors;
    }
    
    public function set_expired_status($ad_id)
    {
        try 
        {
            $ad = ORM::factory('ad', $ad_id)->set('status', 4)->update();
            Model::factory('email')->status_ad_email($ad->ad_zone->publisher,
                $ad->advertiser, $this->get_status_name(4));
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
    
    public function set_first_date($ad_id)
    {
        try 
        {
            ORM::factory('ad', $ad_id)->set('first_date', 
                Helper_Date::get_current_mkdate())->update();
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
    
    public function check_expired($ad)
    {
        if($ad->first_date != -1)
        {
            $result = Date::span($ad->first_date, 
                Helper_Date::get_current_mkdate(), 'days');
            if($result >= 30 * $ad->quantity)
                $this->set_expired_status($ad->id);
        }
        return true;
    }

    private function fields_validation($fields)
    {
        $validation = Validation::factory($fields);
        $validation
            ->rule('checkbox', 'not_empty')
            ->rule('checkbox', 'Model_User::checkbox_exist', array(':value'));
        if(!$validation->check())
        {
            $this->errors = $validation->errors('register');
            $this->set_error_message();
            return false;
        }
        return true;
    }
    
    private function set_error_message()
    {
        if(isset($this->errors['email']) AND $this->errors['email'] == 
                'register/user.email.unique')
            $this->errors['email'] = 'This email is already taken';
        if(isset($this->errors['host']) AND $this->errors['host'] == 
                'register.host.Model_User::unique_name_exists')
            $this->errors['host'] = 'This website is already taken';
    }
    
    public function check_old_ads()
    {
        $ads = ORM::factory('ad')->find_all();
        foreach ($ads as $ad)
        {
            if($ad->first_date == 0)
            {
                $reset_date = strtotime('+7 day', strtotime($ad->date));
                if(time() > $reset_date)
                {
                    Model::factory('image')->delete_banner($ad->banner_path);
                    $ad->delete();
                }
            }
        }
    }
    
    private function get_status_name($status_id)
    {
        $result = '';
        switch ($status_id) 
        {
            case 1:
                $result = "active";
                break;
            case 2:
                $result = "paused";
                break;
            case 3:
                $result = "rejected";
                break;
            case 4:
                $result = "expired";
                break;
            case 5:
                $result = "paused";
                break;
            case 6:
                $result = "paused";
                break;
        }
        return $result;
    }
}
