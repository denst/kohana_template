<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Public_StaticPages extends Controller_Public_App {
    
    public function action_view() 
    {
        $url = $this->request->param('url');
        $current_url = Model::factory('staticpages')->get_page_by_url($url);
        if($current_url)
        {
            $this->page_title($current_url->title);
            $this->template->content = View::factory('staticpage')
                ->set('static_page', $current_url);
        }
        else
            throw new HTTP_Exception_404;
    }
}
