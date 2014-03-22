<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Plans_Plan extends ORM {
    
    protected $_table_name = 'plans';
    
    public function get_all_plans()
    {
        return ORM::factory('plans_plan')->find_all();
    }
    
    public function get_plan_by_id($id)
    {
        return Helper_Values::get_value('plans_plan', $id);
    }
        
    public function get_plan_by_fields($field, $value)
    {
        $value = (int)$value;
        return Helper_Values::get_value('plans_plan', false, $field, $value);
    }
}
