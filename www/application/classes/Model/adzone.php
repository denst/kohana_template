<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_AdZone extends ORM {
    
    private $errors = array();
    
    protected $_table_name = 'ad_zones';
    
    protected $_belongs_to = array(
        'units_category' => array('model' => 'unitscategory'),
        'publisher'     => array(
             'model' => 'user',
             'foreign_key' => 'publisher_id',
        ),
    );

    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty')),
            'size' => array(
                array('not_empty')),
        );
    }
    
    public function create_zone($post)
    {
        if($this->fields_validation($post))
        {
            try 
            {
                $post['size'] = json_encode($post['size']);
                if(empty($post['name']))
                    $post['name'] = " ";
                if($post['min_quantity'] == 0)
                    $post['min_quantity'] = 1;
                ORM::factory('adzone')
                    ->values($post)
                    ->create();
                return true;
            }
            catch (ORM_Validation_Exception $exc) 
            {
                $this->errors = $exc->errors();
                return false;
            }
        }
        else
            return false;
    }
    
    public function delete_zone($zone_id)
    {
        try 
        {
            $ads = Model::factory('ad')
                    ->get_ads_by_ad_zone_id($zone_id);
            foreach ($ads as $ad)
            {
                Model::factory('ad')->delete_ad($ad);
            }
            ORM::factory('adzone', $zone_id)->delete();
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
        
    }
    
    public function changestatus($post)
    {
        try 
        {
            $zone = ORM::factory('adzone', $post['zone_id']);
            if($post['status'] == 'On')
                $status = 'Off';
            else
                $status = 'On';
            $zone->set('status', $status);
            $zone->update();
            return $status;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            ProfilerToolbar::add_log('error', $exc->getMessage(), true);
            return false;
        }
    }
    
    public function get_zone_by_id($id)
    {
        return Helper_Values::get_value('adzone', $id);
    }
    
    public function get_adzone_size($ad_zone_id)
    {
        return json_decode(ORM::factory('adzone', $ad_zone_id)->size);
    }
    
    public function get_zones_by_publisher_id($publisher_id)
    {
        $all_zones = ORM::factory('adzone')
            ->where('publisher_id', '=', $publisher_id)
            ->order_by('id', 'DESC')
            ->find_all();
        return $all_zones;
    }
    
    public function check_adzone_tariff($ad, $type)
    {
        $ad_zone = $ad->ad_zone;
        $category = $ad->ad_zone->units_category->name;
        if($category == $type)
        {
            switch($category)
            {
                case 'clicks':
                    if(Valid::not_empty($ad->quantity) AND
                            $ad->total_clicks >= $ad_zone->for_quantity * $ad->quantity)
                        Model::factory('ad')->set_expired_status($ad->id);
                    break;
                case 'impressions':
                    if(Valid::not_empty($ad->quantity) AND
                            $ad->total_impressions >= $ad_zone->for_quantity * $ad->quantity)
                        Model::factory('ad')->set_expired_status($ad->id);
                    break;
            }
        }
        return true;
    }
    
    public function check_adzone_available_count($ad_zone)
    {
        $ad_count = Model::factory('ad')->get_ads_count_by_adzone_id($ad_zone->id);
        if($ad_count == $ad_zone->ad_count)
            return false;
        else
            return true;
    }
    
    public function check_expired_ads_by_zones()
    {
        $ad_zones = $this->get_all_adzone();
        foreach ($ad_zones as $ad_zone)
        {
            if($ad_zone->units_category_id == 3)
            {
                $ads = Model::factory('ad')->get_ads_by_ad_zone_id($ad_zone->id);
                foreach ($ads as $ad)
                {
                    Model::factory('ad')->check_expired($ad);
                }
            }
        }
    }
    
    public function get_all_adzone()
    {
        $ad_zones = ORM::factory('adzone')->find_all();
        return $ad_zones;
    }
    
    public function get_errors()
    {
        return $this->errors;
    }

    public static function check_zero($value)
    {
        if($value != 0)
            return true;
        else
            return false;
    }

    private function fields_validation($fields)
    {
        $validation = Validation::factory($fields);
        $validation
            ->rule('price', 'numeric')
            ->rule('price', 'Model_AdZone::check_zero', array(':value'))
            ->rule('for_quantity', 'Model_AdZone::check_zero', array(':value'));
        if(!$validation->check())
        {
            $this->errors = $validation->errors('register');
            return false;
        }
        return true;
    }
}