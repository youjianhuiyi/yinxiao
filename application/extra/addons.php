<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'admin_login_init' => 
    array (
      0 => 'loginbg',
    ),
    'app_init' => 
    array (
      0 => 'xunsearch',
    ),
  ),
  'route' => 
  array (
    '/qrcode$' => 'qrcode/index/index',
    '/qrcode/build$' => 'qrcode/index/build',
    '/xunsearch$' => 'xunsearch/index/index',
    '/xunsearch/[:name]' => 'xunsearch/index/search',
  ),
);