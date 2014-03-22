<?php defined('SYSPATH') or die('No direct script access.');

class Error extends Controller_Public_App {
    
    public static function handle(Exception $e)
    {
        switch (get_class($e))
        {
            case 'HTTP_Exception_404':
                $response = new Response;
                $response->status(404);
                $view = View::factory('template/index')
                    ->set('title','')
                    ->set('styles',array(
                        'css/ads.css' => 'screen',
                        'css/bootstrap.min.css' => 'screen',
                        'css/font-awesome.min' => 'screen'))
                    ->set('scripts',array());
                $view->content = View::factory('errors/404');
                echo $response->body($view);
                return TRUE;
                break;
            default:
                return Kohana_Exception::handler($e);
                break;
        }
    }
}