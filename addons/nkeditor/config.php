<?php

return array (
  0 => 
  array (
    'name' => 'theme',
    'title' => '编辑器主题',
    'type' => 'select',
    'content' => 
    array (
      'default' => '经典主题',
      'black' => '雅黑主题',
      'blue' => '淡蓝主题',
      'grey' => '深灰主题',
      'primary' => '深绿主题',
    ),
    'value' => 'grey',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'attachmentmode_admin',
    'title' => '管理员附件选择模式',
    'type' => 'select',
    'content' => 
    array (
      'all' => '任何管理员均可以查看全部上传的文件',
      'auth' => '仅可以查看自己及所有子管理员上传的文件',
      'personal' => '仅可以查看选择自己上传的文件',
    ),
    'value' => 'all',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'attachmentmode_index',
    'title' => '痈台附件选择模式',
    'type' => 'select',
    'content' => 
    array (
      'all' => '任何会员均可以查看全部上传的文件',
      'personal' => '仅可以查看选择自己上传的文件',
    ),
    'value' => 'all',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
