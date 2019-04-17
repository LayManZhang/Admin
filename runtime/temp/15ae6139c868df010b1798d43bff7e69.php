<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:44:"E:\fadmin\addons\cms\view\default\index.html";i:1552545185;s:52:"E:\fadmin\addons\cms\view\default\common\layout.html";i:1554969375;s:50:"E:\fadmin\addons\cms\view\default\common\item.html";i:1555401699;s:53:"E:\fadmin\addons\cms\view\default\common\sidebar.html";i:1552545185;}*/ ?>
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

        <link rel="stylesheet" media="screen" href="/assets/addons/cms/css/common.css?v=<?php echo \think\Config::get("site.version"); ?>" />
        <link rel="stylesheet" media="screen" href="/assets/addons/cms/css/font-awesome.min.css?v=<?php echo \think\Config::get("site.version"); ?>" />

        <link rel="stylesheet" media="screen" href="/assets/addons/cms/css/jquery.colorbox.css?v=<?php echo \think\Config::get("site.version"); ?>" />
        <link rel="stylesheet" href="//at.alicdn.com/t/font_1461494259_6884313.css">
        <link rel="stylesheet" href="//at.alicdn.com/t/font_1461494259_6884313.css">

        <!--[if lt IE 9]>
          <script src="/libs/html5shiv.js?v=<?php echo \think\Config::get("site.version"); ?>"></script>
          <script src="/libs/respond.min.js?v=<?php echo \think\Config::get("site.version"); ?>"></script>
        <![endif]-->

        <script type="text/javascript" src="/assets/libs/jquery/dist/jquery.min.js"></script>
        <script type="text/javascript" src="/assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/assets/addons/cms/js/bootstrap-typeahead.min.js"></script>
        <script type="text/javascript" src="/assets/addons/cms/js/common.js"></script>
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
                            <?php $__feVXZv3SBt__ = \addons\cms\model\Channel::getChannelList(["id"=>"nav","type"=>"top"]); if(is_array($__feVXZv3SBt__) || $__feVXZv3SBt__ instanceof \think\Collection || $__feVXZv3SBt__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__feVXZv3SBt__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i;?>
                            <!--判断是否有子级或高亮当前栏目-->
                            <li class="<?php if($nav['has_child']): ?>dropdown<?php endif; if($__CHANNEL__&&($__CHANNEL__['id']==$nav['id']||$__CHANNEL__['parent_id']==$nav['id'])): ?> active<?php endif; ?>">
                                <a href="<?php echo $nav['url']; ?>"<?php if($nav['has_child']): ?> data-toggle="dropdown"<?php endif; ?>><?php echo $nav['name']; if($nav['has_child']): ?>  <b class="caret"></b><?php endif; ?></a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php $__v0X3kWUQPn__ = \addons\cms\model\Channel::getChannelList(["id"=>"sub","type"=>"son","typeid"=>$nav['id']]); if(is_array($__v0X3kWUQPn__) || $__v0X3kWUQPn__ instanceof \think\Collection || $__v0X3kWUQPn__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__v0X3kWUQPn__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?>
                                    <li><a href="<?php echo $sub['url']; ?>"><?php echo $sub['name']; ?></a></li>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </ul>
                            </li>
                            <?php endforeach; endif; else: echo "" ;endif; ?>

                            <!--实现无限级下拉菜单,maxlevel来控制最大层级-->
                            <!--<?php $__tjwDsbygG3__ = \addons\cms\model\Channel::getNav(isset($__CHANNEL__)?$__CHANNEL__:[], ["maxlevel"=>"3","cache"=>"86400"]); ?><?php echo $__tjwDsbygG3__; ?>-->
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
    <section class="swiper-container index-focus">
        <div class="row">
            <div class="col-md-8">
                <!-- S 焦点图 -->
                <div id="carousel-focus-captions" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php $__o8IXY2AkTL__ = \addons\cms\model\Block::getBlockList(["id"=>"block","name"=>"focus","row"=>"5"]); if(is_array($__o8IXY2AkTL__) || $__o8IXY2AkTL__ instanceof \think\Collection || $__o8IXY2AkTL__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__o8IXY2AkTL__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$block): $mod = ($i % 2 );++$i;?>
                        <li data-target="#carousel-focus-captions" data-slide-to="<?php echo $i-1; ?>" class="<?php if($i==1): ?>active<?php endif; ?>"></li>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </ol>
                    <div class="carousel-inner" role="listbox">
                        <?php $__5zTgfbUHeD__ = \addons\cms\model\Block::getBlockList(["id"=>"block","name"=>"focus","row"=>"5"]); if(is_array($__5zTgfbUHeD__) || $__5zTgfbUHeD__ instanceof \think\Collection || $__5zTgfbUHeD__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__5zTgfbUHeD__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$block): $mod = ($i % 2 );++$i;?>
                        <div class="item <?php if($i==1): ?>active<?php endif; ?>">
                            <a href="<?php echo $block['url']; ?>">
                                <div class="carousel-img" style="background-image:url('<?php echo $block['image']; ?>');"></div>
                                <div class="carousel-caption hidden">
                                    <h3><?php echo $block['title']; ?></h3>
                                    <?php if($block['content']): ?><p><?php echo $block['content']; ?></p><?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                    <a class="left carousel-control" href="#carousel-focus-captions" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel-focus-captions" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                <!-- E 焦点图 -->
            </div>
            <div class="col-md-4  course-sidebar">
                <div class="panel panel-default lasest-update">
                    <!-- S 最近更新 -->
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo __('Recently update'); ?></h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled">
                        <?php $__GtEj3dkqNA__ = \addons\cms\model\Archives::getArchivesList(["id"=>"new","row"=>"8","orderby"=>"id","orderway"=>"desc"]); if(is_array($__GtEj3dkqNA__) || $__GtEj3dkqNA__ instanceof \think\Collection || $__GtEj3dkqNA__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__GtEj3dkqNA__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$new): $mod = ($i % 2 );++$i;?>
                        <li>
                            <span>[<a href="<?php echo $new['channel']['url']; ?>"><?php echo $new['channel']['name']; ?></a>]</span>
                            <a class="link-dark" href="<?php echo $new['url']; ?>" title="<?php echo $new['title']; ?>"><?php echo $new['title']; ?></a>
                        </li>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                    <!-- E 最近更新 -->
                </div>
            </div>
        </div>
    </section>

    <div style="margin-bottom:20px;">
        <a href="http://www.fastadmin.net"><img src="/assets/addons/cms/img/banner/1.jpg" class="img-responsive"/></a>
    </div>

    <div class="article-list-body row">

        <div class="col-md-8 article-list-main">
            <!-- S 首页列表 -->
            <?php $__te29EsirYX__ = \addons\cms\model\Archives::getArchivesList(["id"=>"item","model"=>1,"addon"=>"true"]); if(is_array($__te29EsirYX__) || $__te29EsirYX__ instanceof \think\Collection || $__te29EsirYX__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__te29EsirYX__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                <section class="article-section">
    <article class="article-item">
        <div class="article-metas clearfix">
            <div class="pull-left">
                <div class="date">
                    <div class="day"><?php echo date('d',$item['createtime']); ?></div>
                    <div class="month"><?php echo date('m',$item['createtime']); ?><?php echo __('Month'); ?></div>
                </div>
            </div>
            <div class="metas-body">
                <h3 class="title"><a href="<?php echo $item['url']; ?>"><?php echo $item['title']; if(isset($item['pro_status'])&&$item['pro_status']==2){echo '（该项目已废止）';} ?></a></h3>
                <p><a href="<?php echo $item['channel']['url']; ?>"><?php echo $item['channel']['name']; ?></a></p>
            </div>
        </div>
        <div class="media">
            <?php if($item['hasimage']): ?>
            <div class="media-left">
                <a href="<?php echo $item['url']; ?>">
                    <?php echo $item['imglink']; ?>
                </a>
            </div>
            <?php endif; ?>
            <div class="media-body">
                <?php echo $item['description']; ?>
            </div>
        </div>
        <div class="article-tag">
            <div class="pull-left">
                <?php echo __('Tags'); ?>3：<span itemprop="keywords">
                    <?php if(is_array($item['tagslist']) || $item['tagslist'] instanceof \think\Collection || $item['tagslist'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item['tagslist'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($i % 2 );++$i;?>
                    <a href="<?php echo $tag['url']; ?>" rel="tag"><?php echo $tag['name']; ?></a><?php if(isset($__LIST__[$i])): ?>,<?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </span>
            </div>
            <div class="pull-right">
                <a href="<?php echo $item['url']; ?>" class="btn btn-success"><i class="fa fa-angle-right"></i> <?php echo __('View more'); ?></a>
            </div>
            <div class="clearfix"></div>
        </div>
    </article>
</section>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            <!-- E 首页列表 -->
        </div>

       <aside class="col-md-4 article-sidebar">
            <!-- S 热门资讯 -->
<div class="panel panel-default hot-article">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo __('Hot news'); ?></h3>
    </div>
    <div class="panel-body">
        <?php $__PhcmLTNj9t__ = \addons\cms\model\Archives::getArchivesList(["id"=>"hot","row"=>"10","orderby"=>"id","orderway"=>"asc"]); if(is_array($__PhcmLTNj9t__) || $__PhcmLTNj9t__ instanceof \think\Collection || $__PhcmLTNj9t__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__PhcmLTNj9t__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$hot): $mod = ($i % 2 );++$i;?>
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
        <?php $__aQ1ZPL6yCf__ = \addons\cms\model\Tags::getTagsList(["id"=>"tag","orderby"=>"rand","limit"=>"30"]); if(is_array($__aQ1ZPL6yCf__) || $__aQ1ZPL6yCf__ instanceof \think\Collection || $__aQ1ZPL6yCf__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__aQ1ZPL6yCf__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($i % 2 );++$i;?>
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
        <?php $__0N9RtjCdbu__ = \addons\cms\model\Archives::getArchivesList(["id"=>"hot","row"=>"10","flag"=>"recommend|new","orderby"=>"id","orderway"=>"asc"]); if(is_array($__0N9RtjCdbu__) || $__0N9RtjCdbu__ instanceof \think\Collection || $__0N9RtjCdbu__ instanceof \think\Paginator): $i = 0; $__LIST__ = $__0N9RtjCdbu__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$hot): $mod = ($i % 2 );++$i;?>
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
            <div class="panel panel-adimg">
                <a href="http://www.fastadmin.net"><img src="/assets/addons/cms/img/sidebar/1.jpg" class="img-responsive"/></a>
            </div>
        </aside>
    </div>
</div>



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