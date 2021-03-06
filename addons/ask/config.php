<?php

return array (
  0 => 
  array (
    'name' => 'system_user_id',
    'title' => '平台会员ID',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '用于统计平台收入的会员ID',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'admin_user_ids',
    'title' => '前台管理员会员ID集合',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '1',
    'rule' => '',
    'msg' => '',
    'tip' => '前台可以管理问题、文章或标签的会员ID集合',
    'ok' => '',
    'extend' => 'class="form-control selectpage" data-source="user/user/index" data-field="nickname" data-multiple="true"',
  ),
  2 => 
  array (
    'name' => 'peepprice',
    'title' => '付费偷看金额区间',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '0.1-10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '用户只能将付费偷看的金额设置在此区间',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'peeppricelist',
    'title' => '付费偷看金额列表',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      '￥1' => '1',
      '￥2' => '2',
      '￥5' => '5',
      '￥10' => '10',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'name' => 'peepanswerratio',
    'title' => '付费偷看分成',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '0.3:0.1:0.6',
    'rule' => 'required',
    'msg' => '',
    'tip' => '平台:提问者:回答者 <br>请保证三者相加为1',
    'ok' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'name' => 'bestanswerratio',
    'title' => '最佳答案分成',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '0.3:0.7',
    'rule' => 'required',
    'msg' => '',
    'tip' => '平台:回答者 <br>请保证两者相加为1',
    'ok' => '',
    'extend' => '',
  ),
  6 => 
  array (
    'name' => 'articleratio',
    'title' => '付费文章分成',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '0.3:0.7',
    'rule' => 'required',
    'msg' => '',
    'tip' => '平台:文章作者 <br>请保证两者相加为1',
    'ok' => '',
    'extend' => '',
  ),
  7 => 
  array (
    'name' => 'inviteprice',
    'title' => '付费邀请赏金区间',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '1-10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '用户只能将付费邀请的赏金设置在此区间',
    'extend' => '',
  ),
  8 => 
  array (
    'name' => 'postanswerlimittype',
    'title' => '发表评论限制方式',
    'type' => 'radio',
    'content' => 
    array (
      'single' => '只能发表一次',
      'multiple' => '可多次发表回答',
    ),
    'value' => 'single',
    'rule' => 'required',
    'msg' => '',
    'tip' => '在对问题发表回答时，数量的限制',
    'ok' => '',
    'extend' => '',
  ),
  9 => 
  array (
    'name' => 'score',
    'title' => '获取积分设置',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'postquestion' => 3,
      'postanswer' => 1,
      'postarticle' => 5,
      'bestanswer' => 10,
      'postcomment' => 0,
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '如果问题或评论被删除则会扣除相应的积分',
    'extend' => '',
  ),
  10 => 
  array (
    'name' => 'limitscore',
    'title' => '限定积分设置',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'postquestion' => 0,
      'postanswer' => 0,
      'postarticle' => 0,
      'postcomment' => 0,
      'peepsetting' => 100,
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '必须达到相应的积分限制条件才可以操作',
    'extend' => '',
  ),
  11 => 
  array (
    'name' => 'default_question_image',
    'title' => '默认问题图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/ask/img/question.png',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  12 => 
  array (
    'name' => 'default_tag_image',
    'title' => '默认话题图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/ask/img/tag.png',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  13 => 
  array (
    'name' => 'default_block_image',
    'title' => '默认区块图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/ask/img/block.png',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  14 => 
  array (
    'name' => 'default_category_img',
    'title' => '默认分类图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/ask/img/category.png',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  15 => 
  array (
    'name' => 'storage',
    'title' => '存储方式',
    'type' => 'radio',
    'content' => 
    array (
      'local' => '本地存储',
      'upyun' => '又拍云',
      'qiniu' => '七牛',
    ),
    'value' => 'local',
    'rule' => 'required',
    'msg' => '',
    'tip' => '请确保已安装对应的上传插件，不支持其它的存储插件',
    'ok' => '',
    'extend' => '',
  ),
  16 => 
  array (
    'name' => 'pricelist',
    'title' => '悬赏金额列表',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      '￥10' => '10',
      '￥20' => '20',
      '￥30' => '30',
      '￥50' => '50',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  17 => 
  array (
    'name' => 'minprice',
    'title' => '付费悬赏最低金额',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => 1,
    'rule' => 'required',
    'msg' => '',
    'tip' => '如果设定为付费悬赏，最低的悬赏金额',
    'ok' => '',
    'extend' => '',
  ),
  18 => 
  array (
    'name' => 'iscustomprice',
    'title' => '任意金额悬赏',
    'type' => 'radio',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '开启后将支持自定义价格',
    'ok' => '',
    'extend' => '',
  ),
  19 => 
  array (
    'name' => 'isanonymous',
    'title' => '会员匿名提问',
    'type' => 'radio',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '开启匿名提问后将不再展示作者信息',
    'ok' => '',
    'extend' => '',
  ),
  20 => 
  array (
    'name' => 'adoptdays',
    'title' => '提问有效期(天)',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => '7',
    'rule' => 'required',
    'msg' => '',
    'tip' => '提问超过指定天数后话题将无法享受分成',
    'ok' => '',
    'extend' => '',
  ),
  21 => 
  array (
    'name' => 'isstillanswer',
    'title' => '是否允许采纳后回答',
    'type' => 'radio',
    'content' => 
    array (
      1 => '可以继续回答',
      0 => '不再允许回答',
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '采纳最佳答案后是否允许继续回答',
    'ok' => '',
    'extend' => '',
  ),
  22 => 
  array (
    'name' => 'isarticlepaidcomment',
    'title' => '文章是否只允许付费后可评论',
    'type' => 'radio',
    'content' => 
    array (
      1 => '是',
      0 => '否',
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '付费文章是否只允许付费后才可评论',
    'ok' => '',
    'extend' => '',
  ),
  23 => 
  array (
    'name' => 'pagesize',
    'title' => '分页大小设置',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'question' => '30',
      'article' => '30',
      'answer' => '50',
      'comment' => '10',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '设定问题、文章、回答、评论的分页大小',
    'ok' => '',
    'extend' => '',
  ),
  24 => 
  array (
    'name' => 'maxinvitelimit',
    'title' => '每日免费邀请次数上限',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '每日免费邀请超过指定上限后将无法发送邀请',
    'ok' => '',
    'extend' => '',
  ),
  25 => 
  array (
    'name' => 'reportlimit',
    'title' => '举报次数阀值',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => '3',
    'rule' => 'required',
    'msg' => '',
    'tip' => '超过举报次数阀值后将关闭问题、文章或回答,并不可见',
    'ok' => '',
    'extend' => '',
  ),
  26 => 
  array (
    'name' => 'isaudit',
    'title' => '内容审核',
    'type' => 'radio',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '开启内容审核后，违法或广告内容都将发布失败',
    'ok' => '',
    'extend' => '',
  ),
  27 => 
  array (
    'name' => 'audittype',
    'title' => '审核方式',
    'type' => 'radio',
    'content' => 
    array (
      'local' => '本地',
      'baiduyun' => '百度云',
    ),
    'value' => 'local',
    'rule' => 'required',
    'msg' => '',
    'tip' => '如果启用百度云，请输入百度云AI平台应用的AK和SK',
    'ok' => '',
    'extend' => '',
  ),
  28 => 
  array (
    'name' => 'nlptype',
    'title' => '分词方式',
    'type' => 'radio',
    'content' => 
    array (
      'local' => '本地',
      'baiduyun' => '百度云',
    ),
    'value' => 'local',
    'rule' => 'required',
    'msg' => '',
    'tip' => '如果启用百度云，请输入百度云AI平台应用的AK和SK',
    'ok' => '',
    'extend' => '',
  ),
  29 => 
  array (
    'name' => 'aip_appid',
    'title' => '百度AI平台应用Appid',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '百度云AI开放平台应用AppId',
    'ok' => '',
    'extend' => '',
  ),
  30 => 
  array (
    'name' => 'aip_apikey',
    'title' => '百度AI平台应用Apikey',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '百度云AI开放平台应用ApiKey',
    'ok' => '',
    'extend' => '',
  ),
  31 => 
  array (
    'name' => 'aip_secretkey',
    'title' => '百度AI平台应用Secretkey',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '百度云AI开放平台应用Secretkey',
    'ok' => '',
    'extend' => '',
  ),
  32 => 
  array (
    'name' => 'apikey',
    'title' => 'ApiKey密钥',
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
  33 => 
  array (
    'name' => 'autolinks',
    'title' => '关键字链接',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      '服务器' => 'https://promotion.aliyun.com/ntms/yunparter/invite.html?userCode=t50mdbun',
      '阿里云' => 'https://promotion.aliyun.com/ntms/yunparter/invite.html?userCode=t50mdbun',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '对应的关键字将会自动加上链接',
    'ok' => '',
    'extend' => '',
  ),
  34 => 
  array (
    'name' => 'loadmode',
    'title' => '列表页加载模式',
    'type' => 'radio',
    'content' => 
    array (
      'infinite' => '无限加载模式',
      'paging' => '分页加载模式',
    ),
    'value' => 'paging',
    'rule' => 'required',
    'msg' => '',
    'tip' => '对应的关键字将会自动加上链接',
    'ok' => '',
    'extend' => '',
  ),
  35 => 
  array (
    'name' => 'emailnotice',
    'title' => '邮件通知',
    'type' => 'checkbox',
    'content' => 
    array (
      'firstanswer' => '第一个回答',
      'secondaryanswer' => '第2~N个回答',
      'message' => '收到私信',
      'adoptanswer' => '采纳答案',
      'ceritification' => '专家认证',
      'thanks' => '收到打赏(感谢)',
      'invite' => '收到邀请(普通)',
      'priceinvite' => '收到邀请(付费)',
    ),
    'value' => 'firstanswer,message,adoptanswer,ceritification,thanks,priceinvite',
    'rule' => 'required',
    'msg' => '',
    'tip' => '是否开启邮件消息通知',
    'ok' => '',
    'extend' => '',
  ),
  36 => 
  array (
    'name' => 'sendemailmode',
    'title' => '邮件发送模式',
    'type' => 'radio',
    'content' => 
    array (
      'queue' => '采用队列异步发送(速度快,需配置)',
      'phpmailer' => '采用PHPEmail同步发送(速度慢)',
    ),
    'value' => 'phpmailer',
    'rule' => 'required',
    'msg' => '',
    'tip' => '如果采用异步队列发送，请务必安装好Redis和think-queue，并配置启用队列服务',
    'ok' => '',
    'extend' => '',
  ),
  37 => 
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
  38 => 
  array (
    'name' => 'rewrite',
    'title' => '伪静态',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'index/index' => '/ask/$',
      'tag/index' => '/ask/tags',
      'tag/show' => '/ask/tag/[:id]',
      'question/index' => '/ask/questions',
      'question/show' => '/ask/question/[:id]',
      'article/index' => '/ask/articles',
      'article/show' => '/ask/article/[:id]',
      'user/index' => '/u/[:id]$',
      'user/dispatch' => '/u/[:id]/[:dispatch]$',
      'expert/index' => '/ask/experts$',
      'search/index' => '/ask/search',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  39 => 
  array (
    'name' => '__tips__',
    'title' => '温馨提示',
    'type' => 'other',
    'content' => 
    array (
    ),
    'value' => '1.如果需要将知识付费问答社区绑定到首页,请移除伪静态中的<b>ask/</b><br>2.后台默认不包含富文本编辑器插件，请在<a href="https://www.fastadmin.net/store.html?category=16" target="_blank">插件市场</a>按需要安装<br>3.如果需要启用付费相关功能,请务必安装<a href="https://www.fastadmin.net/store/epay.html" target="_blank">微信支付宝整合</a>插件',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
