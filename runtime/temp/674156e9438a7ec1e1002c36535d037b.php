<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:69:"E:\fadmin\public/../application/index\view\cms\rdsystem\research.html";i:1555575416;s:52:"E:\fadmin\application\index\view\layout\default.html";i:1554877745;s:49:"E:\fadmin\application\index\view\common\meta.html";i:1547349021;s:52:"E:\fadmin\application\index\view\common\sidenav.html";i:1553668112;s:51:"E:\fadmin\application\index\view\common\script.html";i:1547349021;}*/ ?>
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
    input[type="file"]{
        display: none;
    }
    table{
        table-layout : fixed;
    }

    table tbody tr td,table tbody tr th{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<script src="/assets/libs/jquery/dist/jquery.js"></script>
<script src="/assets/libs/fastadmin-layer/dist/layer.js"></script>
<script>
    var item = 0;
    var count = new Array();
    $(function () {
        datalength = $("input[name=btSelectItem]").length;
    });

    function edit(id) {
        var url = "<?php echo url('index/cms.diyform/index?diyname=research'); ?>";
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
                data : {id:ids,diyname:'Research'},
                success : function(data){
                    if(data.code==1){
                        layer.msg(data.msg,{time: 500});
                        var tbody = $('.table tbody').find('tr');
                        var router = layui.router();
                        var page = router.search.comid;
                        if(tbody.length==0){

                        }
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

    function delall() {

        if(count.length<=0){
            layer.msg('未选择数据',{time:1000});return;
        }
        //询问框
        layer.confirm('是否删除多条记录？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            ids = count.join(',');
            $.ajax({
                url:"<?php echo url('index/cms.rdsystem/delete'); ?>",
                type:'post',
                data : {id:ids,diyname:'Research'},
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
    //解决报错但不影响功能
    function SlyarErrors() {
        return true;
    }
    window.onerror = SlyarErrors;
</script>
<script src="/assets/libs/layui/layui.js"></script>
<script>
    layui.use('upload', function(){
        var upload = layui.upload;

        //执行实例
        var uploadInst = upload.render({
            elem: '#btn-import-file' //绑定元素
            ,url: "<?php echo url('index/cms.rdsystem/import'); ?>" //上传接口
            ,accept: 'file' //允许上传的文件类型
            ,exts: 'csv|xls|xlsx'
            ,data: {diyname:'Research'}
            ,done: function(res){
                if(res.code==1){
                    layer.msg('导入成功',{time:1000, end: function () {
                            location.reload();
                        }
                    });
                }else{
                    layer.msg('导入失败',{time:1000, end: function () {
                            location.reload();
                        }
                    });
                }
                //上传完毕回调
            }
            ,error: function(){
                //请求异常回调
                layer.msg('导入失败', {
                    time: 1000, end: function () {
                        location.reload();
                    }
                });
            }
        });
    });

    layui.use('upload', function(){
        var upload = layui.upload;

        //执行实例
        var uploadInst = upload.render({
            elem: '#btn-import-files' //绑定元素
            ,url: "<?php echo url('index/cms.rdsystem/importAll'); ?>" //上传接口
            ,accept: 'file' //允许上传的文件类型
            ,exts: 'csv|xls|xlsx'
            ,data: {}
            ,done: function(res){
                var index = layer.load(1, {
                    shade: [0.1,'#fff'], //0.1透明度的白色背景
                });
                layer.close(index);
                if(res.code==1){
                    layer.msg(res.msg,{time:1000, end: function () {
                            location.reload();
                        }
                    });
                }else{
                    layer.msg(res.msg,{time:1000, end: function () {
                            location.reload();
                        }
                    });
                }
                //上传完毕回调
            }
            ,error: function(){
                layer.close(index);
                //请求异常回调
                layer.msg('服务器异常', {
                    time: 1000, end: function () {
                        location.reload();
                    }
                });
            }
        });
    });
</script>
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
                            <h2>明细账</h2>
                            <em style="font-size: 10px">为保证数据的有效性，请先<a href="<?php echo url('index/cms.rdsystem/importtips'); ?>">下载模板</a>，并根据模板写入数据，再进行导入</em>
                            <br>
                            <a href="<?php echo url('index/cms.diyform/index?diyname=research'); ?>" class="btn btn-success btn-add" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>
                            <a href="javascript:;" class="btn btn-danger btn-del btn-disabled" title="<?php echo __('Delete'); ?>" onclick="delall();"><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>
                            <a href="javascript:;" class="btn btn-danger btn-import" title="<?php echo __('导入明细账'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('导入'); ?></a>
                            <a href="javascript:;" class="btn btn-danger btn-import" title="<?php echo __('导入费用明细账'); ?>" id="btn-import-files" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('导入费用明细'); ?></a>
                        </div>
                        <?php if(!empty($items)){ ?>
                        <div class="row">
                            <table class="table table-bordered table-hover" lay-even lay-skin="nob">
                                <thead>
                                <tr>
                                    <th style="width: 30px;"><input type="checkbox" class="btnSelectAll" name="btSelectAll"  onclick="btSelectAll();"></th>
                                    <th style="width: 85px;">凭证日期</th>
                                    <th style="width: 70px;">凭证号</th>
                                    <th>会计科目</th>
                                    <th style="width: 100px;">摘要</th>
                                    <th style="width: 100px;">费用类别</th>
                                    <th>借方金额</th>
                                    <th>贷方金额</th>
                                    <th style="text-align: center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($items) || $items instanceof \think\Collection || $items instanceof \think\Paginator): $i = 0; $__LIST__ = $items;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                                <tr>
                                    <td><input type="checkbox" class="btnSelectItem" id="btSelectItem<?php echo $item['id']; ?>" name="btSelectItem" val="<?php echo $item['id']; ?>" onclick="btSelectItem(<?php echo $item['id']; ?>);"></td>
                                    <td><?php echo $item['Date']; ?></td>
                                    <td><?php echo $item['Document_number']; ?></td>
                                    <td title="<?php echo $item['Acct_Tit']; ?>"><?php echo $item['Acct_Tit']; ?></td>
                                    <td title="<?php echo $item['Abstract']; ?>"><?php echo $item['Abstract']; ?></td>
                                    <td style="overflow: hidden;" title="<?php echo $item['Category']; ?>"><?php echo $item['Category']; ?></td>
                                    <td><?php echo $item['Debit_amount']; ?></td>
                                    <td><?php echo $item['Credit_amount']; ?></td>
                                    <td style="text-align: center">
                                        <a href="javascript:;" class="edit" onclick="edit(<?php echo $item['id']; ?>)">编辑</a>
                                        <a href="javascript:;" class="edit" onclick="del(<?php echo $item['id']; ?>)">删除</a></td>
                                </tr>
                                <?php endforeach; endif; else: echo "" ;endif; ?>

                                </tbody>
                            </table>
                        </div>
                        <?php }else{ ?>
                        <div class="row" style="text-align: center">
                             <?php echo __('暂无数据'); ?>
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

        </main>

        <footer class="footer" style="clear:both">
            <!-- FastAdmin是开源程序，建议在您的网站底部保留一个FastAdmin的链接 -->
            <p class="copyright">Copyright&nbsp;©&nbsp;2017-2018 Powered by <a href="https://www.fastadmin.net" target="_blank">FastAdmin</a> All Rights Reserved <?php echo $site['name']; ?> <?php echo __('Copyrights'); ?> <a href="http://www.miibeian.gov.cn" target="_blank"><?php echo $site['beian']; ?></a></p>
        </footer>

        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-frontend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>

    </body>

</html>