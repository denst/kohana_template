<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ModuleTemplate extends Controller_Template {
    
    public $template = 'moduletemplate/index';
    
    public function before()
    {
        if ($this->auto_render)
        {
            $this->template = View::factory($this->template);
            $this->template->content = '';
        }
    }

    public function after()
    {
        if ($this->auto_render === TRUE)
        {
            $this->response->body($this->template);
        }
        parent::after();
    }
    
    public function action_index()
    {
    }
}