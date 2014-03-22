<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_AdDateFilter extends ORM {
    
    protected $_table_name = 'ads_datefilter';
    
    public function increase_impression($ad_id)
    {
        try 
        {
            $ad = $this->get_ad($ad_id);
            $impressions = $ad->impressions + 1;
            $ad->set('impressions', $impressions)
                ->update();
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
            $ad = $this->get_ad($ad_id);
            $clicks = $ad->clicks + 1;
            $ad->set('clicks', $clicks)
                ->update();
            return true;
        }
        catch (ORM_Validation_Exception $exc) 
        {
            return false;
        }
    }
    
    public function get_ad($ad_id)
    {
        $ad = ORM::factory('addatefilter')
                ->where('ad_id', '=', $ad_id)
                ->where('date', '=', Helper_Date::get_current_mkdate())
                ->find();
        if($ad->loaded())
            return $ad;
        else
        {
            $ad = ORM::factory('addatefilter')
                ->set('ad_id', $ad_id)
                ->set('date', Helper_Date::get_current_mkdate())
                ->create();
            return $ad;
        }
    }
    
    public function get_all_ads_by_datefilter($start_date, $end_date, $ads_id_array)
    {
        $start_date = strtotime($start_date);
        $end_date = strtotime($end_date);
        $ads = ORM::factory('addatefilter')
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date)
                ->find_all();
        return $this->calc_ads_clicks_impres($ads, $ads_id_array);
    }
    
    public function get_all_ads_without_datefilter()
    {
        $ads_datefilter = ORM::factory('addatefilter')
            ->find_all();
        $ads = Model::factory('ad')->get_all_ads();
        $ads_id_array = array();
        foreach ($ads as $ad) 
        {
            $ads_id_array[] = $ad->id;
        }
        if(count($ads) > 0)
            return $this->calc_ads_clicks_impres($ads_datefilter, $ads_id_array);
        else
            return false;
    }

    private function calc_ads_clicks_impres($ads, $ads_id_array)
    {
        $res = array();
        foreach ($ads as $ad)
        {
            if(in_array($ad->ad_id, $ads_id_array))
            {
                if(empty($res[$ad->ad_id]['clicks']))
                    $res[$ad->ad_id]['clicks'] = $ad->clicks;
                else
                    $res[$ad->ad_id]['clicks'] = 
                        $res[$ad->ad_id]['clicks'] + $ad->clicks;
                
                if(empty($res[$ad->ad_id]['impressions']))
                    $res[$ad->ad_id]['impressions'] = $ad->impressions;
                else
                    $res[$ad->ad_id]['impressions'] = 
                        $res[$ad->ad_id]['impressions'] + $ad->impressions;
            }
        }
        foreach ($ads_id_array as $id_ad) 
        {
            if(! key_exists($id_ad, $res))
            {
                    $res[$id_ad]['clicks'] = 0;
                    $res[$id_ad]['impressions'] = 0;
            }
        }
        return $res;
    }
}