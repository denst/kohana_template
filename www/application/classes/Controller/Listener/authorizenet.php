<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Listener_Authorizenet extends Controller {
    
    public static function set_authorize($post, $ad, $referrer)
    {
        $publisher = $ad->ad_zone->publisher;
        $advertiser = Model::factory('user')->get_user_by_id($post['advertiser_id']);
        $gateways_info = Model::factory('gateways_info')
            ->get_gateways_info($publisher->id);
        $encrypt = Encrypt::instance();
        $sandbox = (Settings::instance()->get_setting('payments') == 'sandbox')? true: false;
        AuthorizeNet::set_authorize_keys($encrypt->decode($gateways_info->authorizenet_api_login_id),
                $encrypt->decode($gateways_info->authorizenet_api_login_key), $sandbox);
        AuthorizeNet::set_autorize_info($post['paid'], $post['cardnumber'], 
                $post['exp_date_month'], $post['exp_date_year'], $post['cvv_number']);
        if(AuthorizeNet::check_card())
        {
            if(! isset($post['quantity']))
                $post['quantity'] = 1;
            Controller_Listener_Main::set_payment(array(
                'ads_id' => $ad->id, 
                'gateway_id' =>  '2',
                'invoice' =>  '',
                'amount' =>  $post['paid'],
                'quantity' =>  $post['quantity'],
                'email' =>  $advertiser->email,
                'date' =>  time(),
                'status' =>  'completed',
            ));
            Request::initial()->redirect(URL::base('http').'advertiser/'.$advertiser->username);
        }
        else
        {
            Helper_Message::add('error', AuthorizeNet::get_errors());
            Session::instance()->set('post', $post);
            Request::initial()->redirect($referrer);
        }
    }
}
