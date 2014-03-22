<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'username' => array(
       'unique' => 'Field username must be unique',
     ),
    'email' => array(
        'unique' => 'Field email must be unique',
     ),
     'terms' => array(
       'not_empty' => "You must agree to the terms",
     ),
);