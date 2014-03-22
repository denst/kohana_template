<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Order extends ORM {
 
    public function is_unique_invoice($invoice)
    {
        if(! Valid::not_empty($invoice)) return true;
        $order = Helper_Values::get_value('order', false, 'invoice', $invoice);
        if($order)
            return false;
        else
            return true;
    }

    public function bill_payment($value)
    {
        try
        {
            ORM::factory('order')
                ->values($value)
                ->set('date', date('Y-m-d H:m:s', $value['date']))
                ->create();
            if($value['status'] == 'completed')
            {
                Model::factory('ad')->set_first_date($value['ads_id']);
                return true;
            }
            else 
                return false;
        }
        catch (ORM_Validation_Exception $e)
        {
            return false;
        }
    }
}