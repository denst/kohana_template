<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Ip2C extends ORM {
    
    protected $_table_name = 'ip2c';
    
    public function get_all_country()
    {
        $countries  = Db::select('countries.country_name', 
            'countries.country_code')
            ->distinct('true')
            ->from(array('ip2c', 'countries'))
            ->order_by('countries.country_name')
            ->as_object()
            ->execute();
        return $countries;
    }
}
