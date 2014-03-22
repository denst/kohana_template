<?php defined('SYSPATH') or die('No direct script access.');
Route::set('activate_assets', 'moduletemplate/<dir>(/<file>)', array('file' => '.+', 'dir' => 
    '(css|js|images)'))
   ->defaults(array(
            'controller' => 'assets',
            'action'     => 'media',
            'file'       => NULL,
            'dir'       => NULL,
    ));

Route::set('activate', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
            'controller' => 'activate',
            'action'     => 'index',
    ));