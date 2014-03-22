<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Template extends ORM {
    
    protected $_table_name = 'template';
    
    protected $_belongs_to = array(
        '' => array('model' => ''),
    );
    
    public function template()
    {
        
    }
}