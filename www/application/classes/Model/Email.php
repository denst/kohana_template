<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Email {
    
    private $error;
    
    public function send($to, $subject, $message)
    {
        $config = Kohana::$config->load('email');
        Email::connect($config);
        $from = Settings::instance()->get_setting('site_email');
        $res = Email::send($to, $from, $subject, $message, $html = true);
        if($res > 0)
            return true;
        else
            return false;
    }
}