<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Settings extends Controller_Admin_App {
    
    public function action_index()
    {
        if(isset($_POST) && Valid::not_empty($_POST))
        {
            $model_setting = Model::factory('setting');
            if(Valid::not_empty($_FILES['banner']['name']))
                $model_setting->change_logo($_FILES);
            if($model_setting->set_settings($_POST))
                Helper_Message::add('success', 'Setting is changed successfully');
        }
        $data = Model::factory('setting')->get_settings();
        $this->template->content = View::factory('admin/settings', $data);
    }
}