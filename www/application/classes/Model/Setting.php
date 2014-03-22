<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Setting extends ORM {
    
    public function set_settings($settings)
    {
        try
        {
            foreach ($settings as $key => $value) 
            {
                $setting = ORM::factory('setting')->where('key', '=', $key)->find(); 
                $setting->set('value', $value)->update();
            }
            parent::clear_cache_entities('settings_cache');
            return true;
        }
        catch (ORM_Validation_Exception $e)
        {
            return false;
        }
    }
    
    public function change_logo($files)
    {
        if(($logo_name = $this->upload_logo($files)))
        {
            try 
            {
                $setting = ORM::factory('setting')->where('key', '=', 'logo')->find(); 
                $setting->set('value', $logo_name)->update();
                Settings::instance()->get_current_settings();
                return true;
            }
            catch (ORM_Validation_Exception $exc) 
            {
                return false;
            }
        }
    }
    
    private function upload_logo($files)
    {
        $txt = 'logo';
        $ext = strtolower(pathinfo($files['banner']['name'], PATHINFO_EXTENSION));
        
        $public = "assets"; 
        $path = "/img/";
        
        $actual_image_name = $txt.".".$ext;
        try 
        {
            $temp = Upload::save($files['banner'], $actual_image_name, APPPATH.$public.'/files/temp/', 0777);
            $logo_name = Settings::instance()->get_setting('logo');
            $old_logo = APPPATH.'assets/img/'.$logo_name;
            unlink($old_logo);
            rename($temp, APPPATH.'assets/img/'.$actual_image_name);
            return $actual_image_name;
        }
        catch (Exception $exc) 
        {
            echo false;
        }
    }

    public static function get_settings()
    {
        if(Kohana::$config->load('ads.enable_project_cache'))
            return parent::cache_execute('Model_Setting::get_settings()', 'settings', array(true));
        
        $settings = array();
        
        $all_settings = ORM::factory('setting')->find_all();
        foreach ($all_settings as $setting) 
        {
            $settings[$setting->key] = $setting->value;
        }
        return $settings;
    }
}