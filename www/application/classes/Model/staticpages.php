<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_StaticPages extends ORM {
    
    protected $_table_name = 'staticpages';
    
    private $errors;

    public function add_page($title, $content)
    {
        if($this->action_page($title, $content))
            return true;
        else
            return false;
    }
    
    public function edit_page($title, $content, $id)
    {
        if($this->action_page($title, $content, $id))
            return true;
        else
            return false;
    }
    
    public function delete_page($id)
    {
        try
        {
            ORM::factory('staticpages', $id)->delete();
            return true;
        }
        catch (ORM_Validation_Exception $e)
        {
            $this->errors = $e->errors();
            return false;
        } 
    }
    
    private function action_page($title, $content, $id = null)
    {
        if($id)
            $static_page = ORM::factory('staticpages', $id);
        else
            $static_page = ORM::factory('staticpages');
        try
        {
            $static_page
                ->set('url', URL::title($title))
                ->set('title', $title)
                ->set('content', $content)
                ->save();
            return true;
        }
        catch (ORM_Validation_Exception $e)
        {
            $this->errors = $e->errors();
            return false;
        }        
    }
    
    public function get_all_pages()
    {
        return ORM::factory('staticpages')->find_all();
    }
    
    public function get_page_by_id($id)
    {
        return Helper_Values::get_value('staticpages', $id);
    }
    
    public function get_page_by_url($url)
    {
        return Helper_Values::get_value('staticpages', false, 'url', $url);
    }

    public function get_errors()
    {
        return $this->errors;
    }
}
