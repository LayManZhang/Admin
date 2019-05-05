<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:48:"E:\fadmin\addons\cms\view\default\show_xmzn.html";i:1552727493;s:57:"E:\fadmin\addons\cms\view\default\common\layout_xmzn.html";i:1552875658;s:53:"E:\fadmin\addons\cms\view\default\common\sidebar.html";i:1552545185;}*/ ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class=""> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1">
        <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <meta name="renderer" content="webkit">
        <title><?php echo \think\Config::get("cms.title"); ?> - <?php echo \think\Config::get("cms.sitename"); ?></title>
        <meta name="keywords" content="<?php echo \think\Config::get("cms.keywords"); ?>" />
        <meta name="description" content="<?php echo \think\Config::get("cms.description"); ?>" />

        <link rel="stylesheet" media="screen" href="/assets/addons/cms/css/show_xmzn.css?v=<?php echo \think\Config::get("site.version"); ?>" />
        <link rel="stylesheet" media="screen" href="/assets/addons/cms/css/font-awesome.min.css?v=<?php echo \think\Config::get("site.version"); ?>" />

        <!--<link rel="stylesheet" media="screen" href="/assets/addons/cms/css/jquery.colorbox.css?v=<?php echo \think\Config::get("site.version"); ?>" />-->
        <link rel="stylesheet" href="//at.alicdn.com/t/font_1461494259_6884313.css">

        <!--[if lt IE 9]>
          <script src="/libs/html5shiv.js?v=<?php echo \think\Config::get("site.version"); ?>"></script>
          <script src="/libs/respond.min.js?v=<?php echo \think\Config::get("site.version"); ?>"></script>
        <![endif]-->
<script type="text/javascript" src="http://www.zhongkechuang.cn/zkc/j/jquery-1.8.3.min.js"></script>
        <script1 type="text/javascript" src="/assets/libs/jquery/dist/jquery.min.js"></script>
        <script1 type="text/javascript" src="/assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
        <script1 type="text/javascript" src="/assets/addons/cms/js/bootstrap-typeahead.min.js"></script>
        <script1 type="text/javascript" src="/assets/addons/cms/js/common.js"></script>
        <script1 type="text/javascript" src="/assets/addons/cms/js/xmzn.js"></script>
    </head>
    <body class="group-page"  >

        <header class="header">
            <!-- S 导航 -->
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="container">

                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="<?php echo \think\Config::get("cms.indexurl"); ?>"><img src="/assets/addons/cms/img/logo.png" width="180" alt=""></a>
                    </div>

                    <div class="collapse navbar-collapse" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                            <!--以下是两种实现导航菜单的方法-->

                            <!--如果你需要自定义NAV,可使用channellist标签来完成,这里只设置了2级-->
                            <?php $__iSLlKPqAoY__ = \addons\cms\model\Channel::getChannelList(["id"=>"nav","type"=>"top"]); if(is_array($__iSLlKPqAoY__) || $__iSLlKPqAoY__ instanceof \think\Collection || $__iSLlKPqAoY__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__iSLlKPqAoY__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i;?>
                            <!--判断是否有子级或高亮当前栏目-->
                            <li class="<?php if($nav['has_child']): ?>dropdown<?php endif; if($__CHANNEL__&&($__CHANNEL__['id']==$nav['id']||$__CHANNEL__['parent_id']==$nav['id'])): ?> active<?php endif; ?>">
                                <a href="<?php echo $nav['url']; ?>"<?php if($nav['has_child']): ?> data-toggle="dropdown"<?php endif; ?>><?php echo $nav['name']; if($nav['has_child']): ?>  <b class="caret"></b><?php endif; ?></a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php $__iJq2Lvr0wI__ = \addons\cms\model\Channel::getChannelList(["id"=>"sub","type"=>"son","typeid"=>$nav['id']]); if(is_array($__iJq2Lvr0wI__) || $__iJq2Lvr0wI__ instanceof \think\Collection || $__iJq2Lvr0wI__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__iJq2Lvr0wI__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?>
                                    <li><a href="<?php echo $sub['url']; ?>"><?php echo $sub['name']; ?></a></li>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>

                            <!--实现无限级下拉菜单,maxlevel来控制最大层级-->
                            <!--<?php $__kBFVue13ql__ = \addons\cms\model\Channel::getNav(isset($__CHANNEL__)?$__CHANNEL__:[], ["maxlevel"=>"3","cache"=>"86400"]); ?><?php echo $__kBFVue13ql__; ?>-->
                        </ul>
                        <ul class="nav navbar-right hidden">
                            <ul class="nav navbar-nav">
                                <li><a href="javascript:;" class="addbookbark"><i class="fa fa-star"></i> 加入收藏</a></li>
                                <li><a href="javascript:;" class=""><i class="fa fa-phone"></i> 联系我们</a></li>
                            </ul>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <form class="form-inline navbar-form" action="<?php echo addon_url('cms/search/index'); ?>" method="get">
                                    <div class="form-search hidden-sm">
                                        <input class="form-control typeahead" name="search" data-typeahead-url="<?php echo addon_url('cms/search/typeahead'); ?>" type="text" id="searchinput" placeholder="搜索">
                                    </div>
                                    <div class="form-search visible-sm">
                                        <a href="javascript:;" class="btn btn-default" id="searchbtn"><i class="fa fa-search"></i></a>
                                    </div>
                                </form>
                            </li>
                            <li class="dropdown">
                                <?php if($user): ?>
                                <a href="<?php echo url('index/user/index'); ?>" class="dropdown-toggle" data-toggle="dropdown" style="padding-top: 10px;height: 50px;">
                                    <span class="avatar-img"><img src="<?php echo cdnurl($user['avatar']); ?>" style="width:30px;height:30px;border-radius:50%;" alt=""></span>
                                </a>
                                <?php else: ?>
                                <a href="<?php echo url('index/user/index'); ?>" class="dropdown-toggle" data-toggle="dropdown">会员<span class="hidden-sm">中心</span> <b class="caret"></b></a>
                                <?php endif; ?>
                                <ul class="dropdown-menu">
                                    <?php if($user): ?>
                                    <li><a href="<?php echo url('index/user/index'); ?>"><i class="fa fa-user fa-fw"></i>会员中心</a></li>
                                    <li><a href="<?php echo url('index/cms.archives/my'); ?>"><i class="fa fa-list fa-fw"></i>我发布的文章</a></li>
                                    <li><a href="<?php echo url('index/cms.archives/post'); ?>"><i class="fa fa-pencil fa-fw"></i>发布文章</a></li>
                                    <li><a href="<?php echo url('index/user/logout'); ?>"><i class="fa fa-sign-out fa-fw"></i>注销</a></li>
                                    <?php else: ?>
                                    <li><a href="<?php echo url('index/user/login'); ?>"><i class="fa fa-sign-in fa-fw"></i>登录</a></li>
                                    <li><a href="<?php echo url('index/user/register'); ?>"><i class="fa fa-user-o fa-fw"></i>注册</a></li>
                                    <?php endif; ?>

                                </ul>
                            </li>
                        </ul>
                    </div>

                </div>
            </nav>
            <!-- E 导航 -->

        </header>

        

<div class="container" id="content-container">

    <div class="article-list-body row">

        <div class="col-md-8 article-detail-main">
            <section class="article-section article-content">
                <ol class="breadcrumb">
                    <!-- S 面包屑导航 -->
                    <?php $__nDxB8rXAl0__ = \addons\cms\model\Channel::getBreadcrumb(isset($__CHANNEL__)?$__CHANNEL__:[], isset($__ARCHIVES__)?$__ARCHIVES__:[], isset($__TAGS__)?$__TAGS__:[], isset($__PAGE__)?$__PAGE__:[]); if(is_array($__nDxB8rXAl0__) || $__nDxB8rXAl0__ instanceof \think\Collection || $__nDxB8rXAl0__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__nDxB8rXAl0__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                    <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                    <!-- E 面包屑导航 -->
                </ol>
                <div class="article-metas">
                    <!-- S 标题区域 -->
                    <div class="metas-body">
                        <h2 class="title"><?php echo $__ARCHIVES__['title']; ?></h2>
                        <div class="sns">
                            <span>
                                发布时间：<?php echo datetime($__ARCHIVES__['createtime']); ?>
                            </span>
                            <span class="views-num">
                                <i class="fa fa-eye"></i><?php echo $__ARCHIVES__['views']; ?>
                            </span>
                        </div>
                      <p><?php echo $__ARCHIVES__['Introduction']; ?></p>
                      <div class="option"><em>资助对象：</em> <?php echo $__ARCHIVES__['Objects_text']; ?></div>
                      <div class="option"><em>申报时间：</em><span class="c2"><?php echo date($__ARCHIVES__['Starttime']); ?>—<?php echo datetime($__ARCHIVES__['Endtime']); ?></span></div>
                    </div>
                    <!-- E 标题区域 -->
                </div>
                <div class="article-text">
                    <!-- S 正文 -->
<div class="high2Left fl">
	<div class="highIntro bg_white">
		<ul class="highIntroType clearfix" id="Content_list">
			<li class="action"><a href="javascript:;">项目介绍</a></li>
			<li><a href="javascript:;">历年申报通知</a></li>
			<li><a href="javascript:;">历年资助企业统计</a></li>
			<li><a href="javascript:;">政策依据</a></li>
		</ul>
		<ul class="IntroCon" style="display: block">
			<li>
                               <p class="ttitle"><strong>　　一、审批内容</strong></p><p>　　<?php echo $__ARCHIVES__['Contents']; ?></p>
                               <p class="ttitle"><strong>　　二、支持力度与方式</strong></p><p>　　<?php echo $__ARCHIVES__['Support']; ?></p>
                               <p class="ttitle"><strong>　　三、申报要求</strong></p><p>　　<?php echo $__ARCHIVES__['Condition']; ?></p>
                               <p class="ttitle"><strong>　　四、申请材料</strong></p><p>　　<?php echo $__ARCHIVES__['Materials']; ?></p>
                               <p class="ttitle"><strong>　　五、受理时间</strong></p><p>　　<?php echo $__ARCHIVES__['Reception_time']; ?></p>
                               <p class="ttitle"><strong>　　六、审批程序</strong></p><p>　　<?php echo $__ARCHIVES__['Procedure']; ?></p>
                               <p class="ttitle"><strong>　　七、其他说明</strong></p><p>　　<?php echo $__ARCHIVES__['Explanation']; ?></p>
			</li>
		</ul>
		<ul class="IntroCon" style="display: none">
			<div class="main">
				<ul class="link-list s2">
					<li><a href="" title=""><span class="fr">2018.12.31</span><i class="fa fa-file-text-o"></i>***项目申报通知</a></li>
				</ul>
			</div>
		</ul>
		<ul class="IntroCon" style="display: none;padding-bottom: 1px;">
			<ul class="list4">
				<li>***项目公示</li>
			</ul>
		</ul>
		<ul class="IntroCon" style="display: none">
			<li><?php echo $__ARCHIVES__['Policy_basis']; ?></li>
		</ul>
	</div>
</div>                               
                    <!-- E 正文 -->
                </div>
            </section>

        </div>

        <aside class="col-md-4 article-sidebar">
            <div class="panel panel-adimg">
              <div class="high1Right">
                <div class="highPing clearfix project_apply" id="21"></div>
                <p class="line30">资助金额：<em> 最高100.00万元</em></p>
                <p class="line30">资助方式：<em> 资金+事前资助+无偿资助</em></p>
                <p class="line20">组织部门： 深圳市科技创新委员会</p>
              </div>
            </div>
            <!-- S 热门资讯 -->
<div class="panel panel-default hot-article">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo __('Hot news'); ?></h3>
    </div>
    <div class="panel-body">
        <?php $__KdeQgcZY60__ = \addons\cms\model\Archives::getArchivesList(["id"=>"hot","row"=>"10","orderby"=>"id","orderway"=>"asc"]); if(is_array($__KdeQgcZY60__) || $__KdeQgcZY60__ instanceof \think\Collection || $__KdeQgcZY60__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__KdeQgcZY60__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$hot): $mod = ($i % 2 );++$i;?>
        <div class="media media-number">
            <div class="media-left">
                <span class="num"><?php echo $i; ?></span>
            </div>
            <div class="media-body">
                <a class="link-dark" href="<?php echo $hot['url']; ?>" title="<?php echo $hot['title']; ?>"><?php echo $hot['title']; ?></a>
            </div>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<!-- E 热门资讯 -->

<!-- S 热门标签 -->
<div class="panel panel-default hot-tags">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo __('Hot tags'); ?></h3>
    </div>
    <div class="panel-body">
        <?php $__37uI6k9QVv__ = \addons\cms\model\Tags::getTagsList(["id"=>"tag","orderby"=>"rand","limit"=>"30"]); if(is_array($__37uI6k9QVv__) || $__37uI6k9QVv__ instanceof \think\Collection || $__37uI6k9QVv__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__37uI6k9QVv__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($i % 2 );++$i;?>
        <a href="<?php echo $tag['url']; ?>"> <span class="badge"><i class="fa fa-tags"></i> <?php echo $tag['name']; ?></span></a>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<!-- E 热门标签 -->

<!-- S 推荐资讯 -->
<div class="panel panel-default recommend-article">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo __('Recommend news'); ?></h3>
    </div>
    <div class="panel-body">
        <?php $__KTLI8hEs5c__ = \addons\cms\model\Archives::getArchivesList(["id"=>"hot","row"=>"10","flag"=>"recommend|new","orderby"=>"id","orderway"=>"asc"]); if(is_array($__KTLI8hEs5c__) || $__KTLI8hEs5c__ instanceof \think\Collection || $__KTLI8hEs5c__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__KTLI8hEs5c__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$hot): $mod = ($i % 2 );++$i;?>
        <div class="media media-number">
            <div class="media-left">
                <span class="num"><?php echo $i; ?></span>
            </div>
            <div class="media-body">
                <a class="link-dark" href="<?php echo $hot['url']; ?>" title="<?php echo $hot['title']; ?>"><?php echo $hot['title']; ?></a>
            </div>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<!-- E 推荐资讯 -->
        </aside>
    </div>
</div>
<script type="text/javascript" src="http://www.zhongkechuang.cn/zkc/j/do.js"></script>


        <footer>
            <div class="container-fluid" id="footer">
                <div class="container">
                    <div class="row footer-inner">
                        <div class="col-md-3 col-sm-3">
                            <div class="footer-logo">
                                <a href="#"><i class="fa fa-bookmark"></i></a>
                            </div>
                            <p class="copyright"><small>© 2019. All Rights Reserved. <br>
                                    中科创
                                </small>
                            </p>
                        </div>
                        <div class="col-md-5 col-md-push-1 col-sm-5 col-sm-push-1">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <ul class="links">
                                        <li><a href="#">关于我们</a></li>
                                        <li><a href="#">发展历程</a></li>
                                        <li><a href="#">服务项目</a></li>
                                        <li><a href="#">团队成员</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <ul class="links">
                                        <li><a href="#">新闻</a></li>
                                        <li><a href="#">资讯</a></li>
                                        <li><a href="#">推荐</a></li>
                                        <li><a href="#">博客</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <ul class="links">
                                        <li><a href="#">服务</a></li>
                                        <li><a href="#">圈子</a></li>
                                        <li><a href="#">论坛</a></li>
                                        <li><a href="#">广告</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3 col-md-push-1 col-sm-push-1">
                            <div class="footer-social">
                                <a href="#"><i class="fa fa-weibo"></i></a>
                                <a href="#"><i class="fa fa-qq"></i></a>
                                <a href="#"><i class="fa fa-wechat"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <div id="floatbtn">
            <!-- S 浮动按钮 -->
            <!--
                <a id="fb-tipoff" class="hover" href="javascript:;" target="_blank">
                <i class="iconfont icon-pencil"></i>
            </a>-->
            <?php if($config['qrcode']): ?>
            <a id="fb-qrcode" href="javascript:;">
                <i class="iconfont icon-qrcode"></i>
                <div id="fb-qrcode-wrapper">
                    <div class="qrcode"><img src="<?php echo $config['qrcode']; ?>"></div>
                    <p>微信公众账号</p>
                    <p>微信扫一扫加关注</p>
                </div>
            </a>
            <?php endif; if(isset($__ARCHIVES__)): ?>
            <a id="feedback" class="hover" href="#comments">
                <i class="iconfont icon-feedback"></i>
            </a>
            <?php endif; ?>
            <a id="back-to-top" class="hover" href="javascript:;">
                <i class="iconfont icon-backtotop"></i>
            </a>
            <!-- E 浮动按钮 -->
        </div>

    </body>
</html>