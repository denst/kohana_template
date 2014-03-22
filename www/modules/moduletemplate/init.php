<?php defined('SYSPATH') or die('No direct script access.');
Route::set('moduletemplate_assets', 'moduletemplate/<dir>(/<file>)', array('file' => '.+', 'dir' => 
    '(css|js|images)'))
   ->defaults(array(
            'controller' => 'assets',
            'action'     => 'media',
            'file'       => NULL,
            'dir'       => NULL,
    ));

Route::set('moduletemplate', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
            'controller' => 'moduletemplate',
            'action'     => 'index',
    ));