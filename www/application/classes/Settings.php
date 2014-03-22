<?php defined('SYSPATH') OR die('No direct access allowed.');

class Settings 
{
    private static $instance;
    
    private $settings = array();

    private function __construct() 
    {
        $this->get_current_settings();
    }
    
    public static function instance()
    {
        if(empty(self::$instance))
            self::$instance = new Settings();
        return self::$instance;
    }
    
    public function set_setting($key, $val)
    {
        $this->settings[$key] = $val;
    }
    
    public function get_settings()
    {
        return $this->settings;
    }
    
    public function get_setting($key)
    {
        return $this->settings[$key];
    }
    
    public function get_current_settings()
    {
        $this->settings = ORM::factory('Setting')->get_settings();
    }
}