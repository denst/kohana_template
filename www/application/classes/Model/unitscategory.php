<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_UnitsCategory extends ORM {
    
    protected $_table_name = 'units_category';
    
    public function get_units_category()
    {
        return ORM::factory('unitscategory')->find_all();
    }
}