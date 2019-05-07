<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'response_send' => 
    array (
      0 => 'ask',
    ),
    'user_register_successed' => 
    array (
      0 => 'ask',
    ),
    'user_delete_successed' => 
    array (
      0 => 'ask',
    ),
    'user_sidenav_after' => 
    array (
      0 => 'cms',
    ),
    'config_init' => 
    array (
      0 => 'nkeditor',
    ),
  ),
  'route' => 
  array (
    '/ask/$' => 'ask/index/index',
    '/ask/tags' => 'ask/tag/index',
    '/ask/tag/[:id]' => 'ask/tag/show',
    '/ask/questions' => 'ask/question/index',
    '/ask/question/[:id]' => 'ask/question/show',
    '/ask/articles' => 'ask/article/index',
    '/ask/article/[:id]' => 'ask/article/show',
    '/u/[:id]$' => 'ask/user/index',
    '/u/[:id]/[:dispatch]$' => 'ask/user/dispatch',
    '/ask/experts$' => 'ask/expert/index',
    '/ask/search' => 'ask/search/index',
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