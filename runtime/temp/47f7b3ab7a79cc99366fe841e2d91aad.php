<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:71:"E:\fadmin\public/../application/index\view\cms\rdsystem\systemlist.html";i:1556446091;s:52:"E:\fadmin\application\index\view\layout\default.html";i:1554877745;s:49:"E:\fadmin\application\index\view\common\meta.html";i:1547349021;s:52:"E:\fadmin\application\index\view\common\sidenav.html";i:1553668112;s:51:"E:\fadmin\application\index\view\common\script.html";i:1547349021;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?> – <?php echo __('The fastest framework based on ThinkPHP5 and Bootstrap'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<?php if(isset($keywords)): ?>
<meta name="keywords" content="<?php echo $keywords; ?>">
<?php endif; if(isset($description)): ?>
<meta name="description" content="<?php echo $description; ?>">
<?php endif; ?>
<meta name="author" content="FastAdmin">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />

<link href="/assets/css/frontend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config: <?php echo json_encode($config); ?>
    };
</script>
        <link href="/assets/css/user.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
    </head>

    <body>

        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#header-navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo url('/'); ?>" style="padding:6px 15px;"><img src="/assets/img/logo.png" style="height:40px;" alt=""></a>
                </div>
                <div class="collapse navbar-collapse" id="header-navbar">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="/" target="_blank"><?php echo __('Home'); ?></a></li>
                        <!--<li><a href="https://www.fastadmin.net/store.html" target="_blank"><?php echo __('Store'); ?></a></li>-->
                        <!--<li><a href="https://www.fastadmin.net/wxapp.html" target="_blank"><?php echo __('Wxapp'); ?></a></li>-->
                        <!--<li><a href="https://www.fastadmin.net/service.html" target="_blank"><?php echo __('Services'); ?></a></li>-->
                        <!--<li><a href="https://www.fastadmin.net/download.html" target="_blank"><?php echo __('Download'); ?></a></li>-->
                        <li><a href="https://www.fastadmin.net/demo.html" target="_blank"><?php echo __('Demo'); ?></a></li>
                        <li><a href="https://www.fastadmin.net/donate.html" target="_blank"><?php echo __('Donation'); ?></a></li>
                        <li><a href="https://forum.fastadmin.net" target="_blank"><?php echo __('Forum'); ?></a></li>
                        <li><a href="https://doc.fastadmin.net" target="_blank"><?php echo __('Docs'); ?></a></li>
                        <li class="dropdown">
                            <?php if($user): ?>
                            <a href="<?php echo url('user/index'); ?>" class="dropdown-toggle" data-toggle="dropdown" style="padding-top: 10px;height: 50px;">
                                <span class="avatar-img"><img src="<?php echo cdnurl($user['avatar']); ?>" alt=""></span>
                            </a>
                            <?php else: ?>
                            <a href="<?php echo url('user/index'); ?>" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('User center'); ?> <b class="caret"></b></a>
                            <?php endif; ?>
                            <ul class="dropdown-menu">
                                <?php if($user): ?>
                                <li><a href="<?php echo url('user/index'); ?>"><i class="fa fa-user-circle fa-fw"></i><?php echo __('User center'); ?></a></li>
                                <li><a href="<?php echo url('user/profile'); ?>"><i class="fa fa-user-o fa-fw"></i><?php echo __('Profile'); ?></a></li>
                                <li><a href="<?php echo url('user/changepwd'); ?>"><i class="fa fa-key fa-fw"></i><?php echo __('Change password'); ?></a></li>
                                <li><a href="<?php echo url('user/logout'); ?>"><i class="fa fa-sign-out fa-fw"></i><?php echo __('Sign out'); ?></a></li>
                                <?php else: ?>
                                <li><a href="<?php echo url('user/login'); ?>"><i class="fa fa-sign-in fa-fw"></i> <?php echo __('Sign in'); ?></a></li>
                                <li><a href="<?php echo url('user/register'); ?>"><i class="fa fa-user-o fa-fw"></i> <?php echo __('Sign up'); ?></a></li>
                                <?php endif; ?>

                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="content">
            <div class="container mt-20 list">
    <div id="content-container" class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="sidenav">
    <?php echo hook('user_sidenav_before'); ?>
    <ul class="list-group">
        <li class="list-group-heading"><?php echo __('User center'); ?></li>
        <li class="list-group-item <?php echo $config['actionname']=='index'?'active':''; ?>"> <a href="<?php echo url('user/index'); ?>"><i class="fa fa-user-circle fa-fw"></i> <?php echo __('User center'); ?></a> </li>
        <li class="list-group-item <?php echo $config['actionname']=='profile'?'active':''; ?>"> <a href="<?php echo url('user/profile'); ?>"><i class="fa fa-user-o fa-fw"></i> <?php echo __('Profile'); ?></a> </li>
        <li class="list-group-item <?php echo $config['actionname']=='changepwd'?'active':''; ?>"> <a href="<?php echo url('user/changepwd'); ?>"><i class="fa fa-key fa-fw"></i> <?php echo __('Change password'); ?></a> </li>
        <li class="list-group-item <?php echo $config['actionname']=='logout'?'active':''; ?>"> <a href="<?php echo url('user/logout'); ?>"><i class="fa fa-sign-out fa-fw"></i> <?php echo __('Sign out'); ?></a> </li>
    </ul>
    <?php echo hook('user_sidenav_after'); ?>
</div>
            </div>
            <div class="col-md-9">
                <div class="panel panel-default panel-user">
                    <div class="panel-body">
                        <div class="page-header panel-post">
                            <h2>研发制度</h2>
                        </div>
                        <?php if(!empty($items)){ ?>
                        <div class="row">
                            <table class="table table-bordered table-hover" lay-even lay-skin="nob">
                                <thead>
                                <tr>
                                    <!--<th style="width: 30px;"><input type="checkbox" class="btnSelectAll" name="btSelectAll"  onclick="btSelectAll();"></th>-->
                                    <th>公司名称</th>
                                    <th>公司简称</th>
                                    <th>研发部门名称</th>
                                    <th style="text-align: center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($items) || $items instanceof \think\Collection || $items instanceof \think\Paginator): $i = 0; $__LIST__ = $items;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                                <tr>
                                    <!--<td><input type="checkbox" class="btnSelectItem" id="btSelectItem<?php echo $item['id']; ?>" name="btSelectItem" val="<?php echo $item['id']; ?>" onclick="btSelectItem(<?php echo $item['id']; ?>);"></td>-->
                                    <td><?php echo $item['company2']; ?></td>
                                    <td><?php echo $item['Compay_abbreviation']; ?></td>
                                    <td><?php echo $item['RD_Department_name']; ?></td>
                                    <td style="text-align: center">
                                        <a href="javascript:;" class="edit" onclick="edit(<?php echo $item['id']; ?>)">编辑</a>
                                        <a href="javascript:;" class="edit" onclick="del(<?php echo $item['id']; ?>)">删除</a></td>
                                </tr>
                                <?php endforeach; endif; else: echo "" ;endif; ?>

                                </tbody>
                            </table>
                            <span>选择要导出的制度名称</span>
                            <select name="hy" id="hy">
                                <option value="制造业">制造业</option>
                                <option value="研发型">研发型</option>
                                <option value="软件业">软件业</option>
                            </select>
                            <select name="yyzd" id="yyzd">
                                <?php if(is_array($yyzd) || $yyzd instanceof \think\Collection || $yyzd instanceof \think\Paginator): $i = 0; $__LIST__ = $yyzd;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $val['title']; ?>"><?php echo $val['title']; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <button onclick="dcWord('pdf');"> <?php echo __('导出PDF'); ?></button>
                            <button onclick="dcWord('word');"> <?php echo __('导出Word'); ?></button>


                        </div>
                        <?php }else{ ?>
                        <div class="row" style="text-align: center">
                            <a href="<?php echo url('index/cms.diyform/index?diyname=Research_system'); ?>"><i class="fa fa-pencil fa-fw"></i> <?php echo __('添加信息'); ?></a>
                        </div>
                        <?php } ?>
                        <hr>
                        <?php echo $data->render(); ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function edit(id) {
        var url = "<?php echo url('index/cms.diyform/index?diyname=Research_system'); ?>";
        url += '?id='+id;
        window.location.href=url;
    }

    function del(id) {
        //询问框
        layer.confirm('是否删除该条记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            var ids = new Array();
            ids.push(id);
            ids = ids.join(',');
            $.ajax({
                url:"<?php echo url('index/cms.rdsystem/delete'); ?>",
                type:'post',
                data : {id:ids,diyname:'Research_system'},
                success : function(data){
                    if(data.code==1){
                        layer.msg(data.msg,{time: 500});
                        location.reload();
                    }else{
                        layer.msg('删除失败',{time: 500});
                    }
                }
            });
        }, function(){
            //
        });
    }


    function dcWord(dc){
        let type = $("#hy").val();
        let category = $("#yyzd").val();
        location.href="/index/cms.rdsystem/dcphpword?dctype="+dc+"&type="+type+'&category='+category;
    }
</script>
        </main>

        <footer class="footer" style="clear:both">
            <!-- FastAdmin是开源程序，建议在您的网站底部保留一个FastAdmin的链接 -->
            <p class="copyright">Copyright&nbsp;©&nbsp;2017-2018 Powered by <a href="https://www.fastadmin.net" target="_blank">FastAdmin</a> All Rights Reserved <?php echo $site['name']; ?> <?php echo __('Copyrights'); ?> <a href="http://www.miibeian.gov.cn" target="_blank"><?php echo $site['beian']; ?></a></p>
        </footer>

        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-frontend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>

    </body>

</html>