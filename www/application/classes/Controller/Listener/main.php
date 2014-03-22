<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Listener_Main extends Controller_Public_App {
    
    public static function set_payment($value)
    {
        $model_orders = Model::factory('order');
        if($model_orders->is_unique_invoice($value['invoice']))
        {
            if($model_orders->bill_payment($value))
            {
                Helper_Message::add('success', 'Thank you for your payment');
                $ad = Model::factory('ad')->get_ad_by_id($value['ads_id']);
                $publisher = $ad->ad_zone->publisher;
                Model::factory('email')->new_ad_email($publisher);
                Model::factory('email')->thankyou_email($ad->advertiser);
                Model::factory('gateways_info')
                    ->set_total_income($publisher, $value['amount']);
                if($publisher->gatewaysinfo->ad_accept == 1)
                    Model::factory('ad')->changestatus(array('status' => $ad->id.'&1'));
            }
        }
    }
}
