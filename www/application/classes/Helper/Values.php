<?php
defined('SYSPATH') or die('No direct access allowed.');

class Helper_Values {
    
        /**
	 * Returns instances of a specific object
         * 
	 * @param string $orm_table Table to which the request
	 * @param int $id Id object
	 * @param string $column Name of column or false
	 * @param string $value Name of value or false
	 * @param boolean $more For all objects
	 * @return object or false
         * 
         * Example:
         *     Helper_Values::get_value('user', $id);
         * 
         * 
	 */
        public static function get_value($orm_table, $id, $column = false, $value = false, $find_all = false)
        {
            if($id)
            {
                $res = ORM::factory($orm_table, $id);
                if($res->loaded())
                    return $res;
                else
                    return false;
            }
            elseif ($find_all)
            {
                $res = ORM::factory($orm_table)->where($column, '=', $value)->find_all();
                if($res->count() != 0)
                    return $res;
                else
                    return false;
            }
            else
                $res = ORM::factory($orm_table)->where($column, '=', $value)->find();
                if($res->loaded())
                    return $res;
                else
                    return false;
        }
        
        public static function get_limit_value($orm_table, $items_per_page, $offset, 
                $column = false, $value = false,  $sort = false, $dir = false)
        {
            $res = ORM::factory($orm_table);
            if($column)
                $res->where($column, '=', $value);
            $res->limit($items_per_page)->offset($offset);
            if($sort)
                $res->order_by($sort, $dir);
            $res = $res->find_all();
            if($res->count() != 0)
                return $res;
            else
                return false;  
        }
        
        public static function get_id_for_note($value)
        {
            $arr = explode('_', $value);
            return $arr[0];
        }
}