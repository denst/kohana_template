<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Listener_Ipn extends Controller_Paypal {
    
    public function action_index() 
    {
        if(parent::action_index())
        {
            $date = strtotime($_POST['payment_date']);
            Controller_Listener_Main::set_payment(array(
                'ads_id' => $_POST['custom'], 
                'gateway_id' =>  '1',
                'invoice' =>  $_POST['txn_id'],
                'amount' =>  $_POST['payment_gross'],
                'quantity' =>  $_POST['quantity'],
                'email' =>  $_POST['payer_email'],
                'date' =>  $date,
                'status' => strtolower($_POST['payment_status']),
                ));
        }
        else
            throw  new HTTP_Exception_404();
    }
}
