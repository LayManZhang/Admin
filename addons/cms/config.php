<?php

return array (
  0 => 
  array (
    'name' => 'sitename',
    'title' => '站点名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '我的CMS网站',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'title',
    'title' => '首页标题',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '中科创',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'keywords',
    'title' => '首页关键字',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '中科创',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'description',
    'title' => '首页描述',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '中科创',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'name' => 'theme',
    'title' => '皮肤',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'default',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'name' => 'qrcode',
    'title' => '公众号二维码',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/cms/img/qrcode.png',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  6 => 
  array (
    'name' => 'default_archives_img',
    'title' => '文档默认图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/cms/img/noimage.jpg',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  7 => 
  array (
    'name' => 'default_channel_img',
    'title' => '栏目默认图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/cms/img/noimage.jpg',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  8 => 
  array (
    'name' => 'default_block_img',
    'title' => '区块默认图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/cms/img/noimage.jpg',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  9 => 
  array (
    'name' => 'default_page_img',
    'title' => '单页默认图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/cms/img/noimage.jpg',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  10 => 
  array (
    'name' => 'domain',
    'title' => '绑定二级域名前缀',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  11 => 
  array (
    'name' => 'rewrite',
    'title' => '伪静态',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'index/index' => '/$',
      'archives/index' => '/cms/a/[:diyname]',
      'tags/index' => '/cms/t/[:name]',
      'page/index' => '/cms/p/[:diyname]',
      'search/index' => '/cms/s',
      'channel/index' => '/cms/c/[:diyname]',
      'diyform/index' => '/cms/d/[:diyname]',
      'channel/getslbm' => '/cms/x/[:diyname]',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  12 => 
  array (
    'name' => 'wxappid',
    'title' => '小程序AppID',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '小程序appid',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  13 => 
  array (
    'name' => 'wxappsecret',
    'title' => '小程序AppSecret',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '小程序secret',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  14 => 
  array (
    'name' => 'apikey',
    'title' => 'ApiKey',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '用于调用API接口时写入数据权限控制<br>可以为空',
    'ok' => '',
    'extend' => '',
  ),
  15 => 
  array (
    'name' => 'archiveseditmode',
    'title' => '文档编辑模式',
    'type' => 'radio',
    'content' => 
    array (
      'addtabs' => '新选项卡',
      'dialog' => '弹窗',
    ),
    'value' => 'dialog',
    'rule' => '',
    'msg' => '',
    'tip' => '在添加或编辑文档时的操作方式',
    'ok' => '',
    'extend' => '',
  ),
  16 => 
  array (
    'name' => '__tips__',
    'title' => '温馨提示',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '1.如果需要将CMS绑定到首页,请移除伪静态中的<b>cms/</b><br>2.默认CMS不包含富文本编辑器插件，请在<a href="https://www.fastadmin.net/store.html?category=16" target="_blank">插件市场</a>按需要安装<br>3.如果需要启用付费阅读,请务必安装<a href="https://www.fastadmin.net/store/epay.html" target="_blank">企业支付</a>插件',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
