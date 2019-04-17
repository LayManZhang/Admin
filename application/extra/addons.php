<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'user_sidenav_after' => 
    array (
      0 => 'cms',
    ),
    'response_send' => 
    array (
      0 => 'loginvideo',
    ),
  ),
  'route' => 
  array (
    '/$' => 'cms/index/index',
    '/cms/a/[:diyname]' => 'cms/archives/index',
    '/cms/t/[:name]' => 'cms/tags/index',
    '/cms/p/[:diyname]' => 'cms/page/index',
    '/cms/s' => 'cms/search/index',
    '/cms/c/[:diyname]' => 'cms/channel/index',
    '/cms/d/[:diyname]' => 'cms/diyform/index',
    '/cms/x/[:diyname]' => 'cms/channel/getslbm',
    '/example$' => 'example/index/index',
    '/example/d/[:name]' => 'example/demo/index',
    '/example/d1/[:name]' => 'example/demo/demo1',
    '/example/d2/[:name]' => 'example/demo/demo2',
  ),
);