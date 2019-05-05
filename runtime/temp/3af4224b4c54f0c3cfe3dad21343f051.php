<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:75:"E:\fadmin\public/../application/index\view\cms\diyform\assessment_list.html";i:1555121986;s:52:"E:\fadmin\application\index\view\layout\default.html";i:1554877745;s:49:"E:\fadmin\application\index\view\common\meta.html";i:1547349021;s:52:"E:\fadmin\application\index\view\common\sidenav.html";i:1553668112;s:51:"E:\fadmin\application\index\view\common\script.html";i:1547349021;}*/ ?>
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
            <style>
    .panel-post {
        position: relative;
    }

    .btn-post {
        position: absolute;
        right: 0;
        bottom: 10px;
    }

    .img-border {
        border-radius: 5px;
        box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
    }
</style>
<script src="/assets/libs/jquery/dist/jquery.js"></script>
<script src="/assets/libs/fastadmin-layer/dist/layer.js"></script>
<script>
    var datalength = 0;
    //查看详情
    function detail(id) {
        $.ajax({
            type: "get",

            url: "<?php echo url('index/cms.diyform/get_xmzn'); ?>",

            data: {"id": id},

            success: function (data) {
                datalength = data.length;
                var content = '  <button class="btn btn-info" type="button" onclick="Export('+id+')" style="margin-left: 20px;"><i class="fa fa-share-square"></i>导出PDF报告</button>' +
                    '<table class="table table-hover table-striped">' +
                    '                                <thead>' +
                    '                                    <tr>' +
                    '                                        <th><input name="btSelectAll" type="checkbox" onclick="btSelectAll();"></th>' +
                    '                                        <th style="width: 85px; " data-field="HighRewardNum" tabindex="0"><div class="th-inner ">是否导出</div><div class="fht-cell"></div></th><th style="width: 150px; " data-field="PEName" tabindex="0"><div class="th-inner ">项目名称</div><div class="fht-cell"></div></th>' +
                    '                                        <th style="width: 150px; " data-field="DeptFullName" tabindex="0"><div class="th-inner ">营业额要求</div><div class="fht-cell"></div></th>                            <th style="width: 150px; " data-field="SupportFrom" tabindex="0"><div class="th-inner ">纳税要求</div><div class="fht-cell"></div></th>' +
                    '                                        <th style="width: 150px; " data-field="AreaFullName" tabindex="0"><div class="th-inner ">资助金额</div><div class="fht-cell"></div></th>                            <th style="width: 150px; " data-field="LevelFullName" tabindex="0"><div class="th-inner ">资助对象</div><div class="fht-cell"></div></th>' +
                    '                                        <th style="width: 150px; " data-field="LevelFullName" tabindex="0"><div class="th-inner ">资助方式</div><div class="fht-cell"></div></th>                            <th style="width: 150px; " data-field="LevelFullName" tabindex="0"><div class="th-inner ">资助方向</div><div class="fht-cell"></div></th>' +
                    '                                        <th style="width: 150px; " data-field="LevelFullName" tabindex="0"><div class="th-inner ">支持领域</div><div class="fht-cell"></div></th>                            <th style="width: 150px; " data-field="LevelFullName" tabindex="0"><div class="th-inner ">项目简介</div><div class="fht-cell"></div></th>' +
                    '                                    </tr>' +
                    '                                </thead>' +
                    '                                <tbody>';
                var html = '';

                $.each(data,function(index,obj) {
                    html += '<tr>' +
                        '                                        <td class="bs-checkbox"><input name="btSelectItem" type="checkbox" val="'+obj.id+'" id="btSelectItem'+obj.id+'" onclick="btSelectItem('+obj.id+');"></td>                                                                <td style="background-color: ; width: 150px; "><span title="'+obj.id+'">导出</span></td>' +
                        '                                        <td style="background-color: ; width: 150px; "><a href="/cms/a/'+obj.id+'.html"> '+obj.title+'</a></td>' +
                        '                                        <td style="background-color: ; width: 150px; "><span title="'+obj.SalesProceeds+'">'+obj.SalesProceeds+'</span></td>' +
                        '                                        <td style="background-color: ; width: 150px; ">'+obj.Ratal+'</td>' +
                        '                                        <td style="background-color: ; width: 150px; "><span title="'+obj.Amount+'">'+obj.Amount+'</span></td>' +
                        '                                        <td style="background-color: ; width: 150px; ">'+obj.Objects_text+'</td>' +
                        '                                        <td style="background-color: ; width: 150px; "><span>'+obj.Mode_text+'</span></td>' +
                        '                                        <td style="background-color: ; width: 150px; ">'+obj.Direction_text+'</td>' +
                        '                                        <td style="background-color: ; width: 150px; "><span>'+obj.Fields_text+'</span></td>' +
                        '                                        <td style="background-color: ; width: 150px; ">'+obj.Introduction+'</td>      ' +
                        '                                    </tr>';
                });

                content += html;
                content += ' </tbody>' +
                    '                            </table>';
                layer.open({
                    type: 1,
                    title: '项目评审',
                    maxmin: true,
                    area: ['1200px', '800px'],
                    content: content,
                    fix: false,
                    shade:0.4,
                });
            }

        });

    };

    function edit(id) {
        var url = "<?php echo url('index/cms.diyform/index?diyname=assessment'); ?>";
        url += '?id='+id;
        window.location.href=url;
    }

    var item = 0;
    var count = [];

    function Export(id) {
        if(item==datalength){
            location.href="/index/cms.diyform/getdata?id="+id;
        }else if(item>0&&item<datalength){
            var ids = count.join(',');
            location.href="/index/cms.diyform/getdata?ids="+ids;
        }else{
            layer.msg('请选择需要导出的数据');
        }
    }


    function btSelectAll(){
        var itemArr = $("input[name=btSelectItem]");
        if($("input[name=btSelectAll]").is(':checked')) {
            item = datalength;
            $("input[name=btSelectItem]").prop('checked',true);
            $.each(itemArr,function (i,d) {
                count.push(parseFloat($(this).attr('val')));
            })
        }else{
            $("input[name=btSelectItem]").prop('checked',false);
            item = 0;
            count=[];
        }
    };

    function btSelectItem(index){
        if($("#btSelectItem"+index).is(':checked')==true) {
            count.push(index);
            item++;
        }else{
            var k = count.indexOf(index);
            if (k > -1) {
                count.splice(k, 1);
            }
            item--;
        }
        if(item==datalength){
            $("input[name=btSelectAll]").prop('checked',true);
        }else{
            $("input[name=btSelectAll]").prop('checked',false);
        }
    }

</script>
<div class="container mt-20 list"  style="display: block">
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
                            <h2><?php echo $title; ?></h2>
                        </div>
                        <?php if(!empty($items)){ ?>
                        <div class="row">
                           <table class="table table-bordered table-hover" lay-even lay-skin="nob">
                               <thead>
                                   <tr>
                                       <th>id</th>
                                       <th>公司名称</th>
                                       <th>评估时间</th>
                                       <!--<th>成立时间</th>-->
                                       <!--<th>产业类型</th>-->
                                       <!--<th>上年度销售收入</th>-->
                                       <!--<th>上年度纳税额</th>-->
                                       <!--<th>添加时间</th>-->
                                       <!--<th>更新时间</th>-->
                                       <th style="text-align: center">操作</th>
                                   </tr>
                               </thead>
                               <tbody>
                                    <?php if(is_array($items) || $items instanceof \think\Collection || $items instanceof \think\Paginator): $i = 0; $__LIST__ = $items;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                                       <tr>
                                           <th><?php echo $item['id']; ?></th>
                                           <th><?php echo $item['company']; ?></th>
                                           <th><?php echo date('Y-m-d H:i:s',$item['updatetime']); ?></th>
                                           <!--<th><?php echo $item['regtime']; ?></th>-->
                                           <!--<th><?php echo $item['fields_text']; ?></th>-->
                                           <!--<th><?php echo $item['SalesProceeds_text']; ?></th>-->
                                           <!--<th><?php echo $item['Ratal']; ?></th>-->
                                           <!--<th><?php echo date('Y-m-d H:i:s',$item['createtime']); ?></th>-->
                                           <!--<th><?php echo date('Y-m-d H:i:s',$item['updatetime']); ?></th>-->
                                           <th style="text-align: center"><a href="javascript:;" class="index" id="<?php echo $item['id']; ?>" onclick="detail(<?php echo $item['id']; ?>);">查看</a>   <a href="javascript:;" class="edit" onclick="edit(<?php echo $item['id']; ?>)">重新评估</a></th>
                                       </tr>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>

                               </tbody>
                           </table>
                        </div>
                        <?php }else{ ?>
                        <div class="row" style="text-align: center">
                            <a href="<?php echo url('index/cms.diyform/index?diyname=assessment'); ?>"><i class="fa fa-pencil fa-fw"></i> <?php echo __('我要评估'); ?></a>
                        </div>
                        <?php } ?>
                        <hr>

                        <div class="pager"><?php echo $xmpgList->render(); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        </main>

        <footer class="footer" style="clear:both">
            <!-- FastAdmin是开源程序，建议在您的网站底部保留一个FastAdmin的链接 -->
            <p class="copyright">Copyright&nbsp;©&nbsp;2017-2018 Powered by <a href="https://www.fastadmin.net" target="_blank">FastAdmin</a> All Rights Reserved <?php echo $site['name']; ?> <?php echo __('Copyrights'); ?> <a href="http://www.miibeian.gov.cn" target="_blank"><?php echo $site['beian']; ?></a></p>
        </footer>

        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-frontend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>

    </body>

</html>