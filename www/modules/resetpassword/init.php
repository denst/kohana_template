<?php defined('SYSPATH') or die('No direct script access.');

Route::set('resetpassword', 'resetpassword(/<action>(/<id>))')
	->defaults(array(
            'controller' => 'resetpassword',
            'action'     => 'index',
    ));