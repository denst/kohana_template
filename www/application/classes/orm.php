<?php defined('SYSPATH') OR die('No direct access allowed.');

class ORM extends Kohana_ORM
{
    public function initialization($model, $user_id)
    {
        $model = ORM::factory($model);
        try
        {
            $model->set('user_id', $user_id)->create();
            return true;
        }
        catch(ORM_Validation_Exception $e)
        {
            return false;
        }
    }

    public function list_columns()
    {
        if(Kohana::$config->load('micrz.enable_project_cache'))
        {
            $cache_lifetime = 360000;
            list($func, $params) = Arr::callback('ORM::list_columns_cache_execute()');
            $params = array($this->_db, $this->_table_name);
            return ORM::caching($this->_table_name ."_structure", $func, $params, $cache_lifetime);
        }
        else
            return parent::list_columns();
    }
    
    public static function list_columns_cache_execute($db, $table_name)
    {
        $columns_data = $db->list_columns($table_name);
        return $columns_data;
    }
    
    public static function cache_execute($method, $name, $pr)
    {
        list($func, $params) = Arr::callback($method);
        $params = $pr;
        return self::caching($name, $func, $params);
    }
    
    public function clear_cache_entities($entities)
    {
        if(Cache::instance()->get($entities))
            Cache::instance()->delete($entities);
        return true;
    }
    
    public static function caching($name, $func, $params, $cache_lifetime = 3600)
    {
        if(Kohana::$config->load('micrz.enable_project_cache'))
        {
            $cache_key = $name."_cache";
            $result = Cache::instance()->get($cache_key);

            if ($result) {
                $current_result = $result;
            }

            if( !isset($current_result)) {
                $current_result = '';
                $current_result = call_user_func_array($func, $params);
                Cache::instance()->set($cache_key, $current_result, $cache_lifetime);
            }
        }
        else
        {
            Cache::instance()->delete_all();
            $current_result = call_user_func_array($func, $params);
        }
        return $current_result;
    }
}
