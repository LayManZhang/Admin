<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:68:"E:\fadmin\public/../application/index\view\cms\rdsystem\project.html";i:1555575550;s:52:"E:\fadmin\application\index\view\layout\default.html";i:1554877745;s:49:"E:\fadmin\application\index\view\common\meta.html";i:1547349021;s:52:"E:\fadmin\application\index\view\common\sidenav.html";i:1553668112;s:51:"E:\fadmin\application\index\view\common\script.html";i:1547349021;}*/ ?>
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
</style>
<script src="/assets/libs/jquery/dist/jquery.js"></script>
<script src="/assets/libs/fastadmin-layer/dist/layer.js"></script>

<script>
    var item = 0;
    var count = new Array();
    $(function () {
        datalength = $("input[name=btSelectItem]").length;

        $("#Selectyear").on('change', function () {
            var year = $(this).val();
            location.href = "/index/cms.rdsystem/project?year=" + year;
        });
    });

    function edit(id) {
        var url = "<?php echo url('index/cms.diyform/index?diyname=Project'); ?>";
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
                data : {id:ids,diyname:'Project'},
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
                data : {id:ids,diyname:'Project'},
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

    function Export(){
        if(count.length==0){
            layer.msg('请选择要导出的项目',{time:500});
        }else if(count.length>1){
            layer.msg('请选择单条进行导出',{time:500});
        }else if(count.length==1){
            let id = count[0];
            let year = $("#Selectyear").val();
            location.href="/index/cms.rdsystem/ExportExcel?id="+id+"&year="+year;
            // window.location.url="<?php echo url('index/cms.rdsystem/ExportExcel'); ?>"+'?id='+count[0];
        }
    }
    function ExportHz(){
        let year = $("#Selectyear").val();
        if(year==''){
            layer.msg('请选择年份',{time:1000});
            return;
        }
        location.href="/index/cms.rdsystem/exportSummary?year="+year;
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
            ,data: {diyname:'Project'}
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
                            <h2>研发项目情况</h2>
                            <em style="font-size: 10px">为保证数据的有效性，请先<a href="<?php echo url('index/cms.rdsystem/importtips'); ?>">下载模板</a>，并根据模板写入数据，再进行导入</em>
                            <br>
                            <a href="<?php echo url('index/cms.diyform/index?diyname=Project'); ?>" class="btn btn-success btn-add" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>
                            <a href="javascript:;" class="btn btn-danger btn-del btn-disabled" onclick="delall();" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>
                            <a href="javascript:;" class="btn btn-danger btn-import" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('导入'); ?></a>
                            <!--<a href="javascript:;" class="btn btn-success btn-export" title="<?php echo __('导出'); ?>" id="btn-export-file" onclick="Export();"><i class="fa fa-download"></i> <?php echo __('辅助账导出'); ?></a>-->
                            <!--<a href="javascript:;" class="btn btn-success btn-export" title="<?php echo __('汇总导出'); ?>" id="btn-export-file-hz" onclick="ExportHz();"><i class="fa fa-download"></i> <?php echo __('辅助账汇总导出'); ?></a>-->
                        </div>
                        <form class="form-horizontal form-commonsearch nice-validator n-default n-bootstrap" novalidate="" method="get" action="/index/cms.rdsystem/project">
                            <fieldset>
                                <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4"><label for="status" class="control-label col-xs-4">年份</label>
                                    <div class="col-xs-8">
                                        <!--<input type="hidden" class="form-control operate" name="year" data-name="year" value="=" readonly="">-->
                                        <select class="form-control" name="year" id="Selectyear">
                                            <option value="">选择</option>
                                            <option value="2019" <?php echo $year=="2019" ? "selected" : ''; ?>>2019</option>
                                            <option value="2018" <?php echo $year=="2018" ? "selected" : ''; ?>>2018</option>
                                            <option value="2017" <?php echo $year=="2017" ? "selected" : ''; ?>>2017</option>
                                            <option value="2016" <?php echo $year=="2016" ? "selected" : ''; ?>>2016</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="col-sm-8 col-xs-offset-4">
                                        <!--<button type="submit" class="btn btn-success" formnovalidate="">筛选</button>-->
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                        <?php if(!empty($items)){ ?>
                        <div class="row">
                            <table class="table table-bordered table-hover" lay-even lay-skin="nob">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" class="btnSelectAll" name="btSelectAll" onclick="btSelectAll();"></th>
                                    <!--<th>id</th>-->
                                    <th>项目编号</th>
                                    <th>研发项目名称</th>
                                    <th>项目起始日期</th>
                                    <th>项目完成日期</th>
                                    <th>研发经费总预算</th>
                                    <th style="text-align: center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($items) || $items instanceof \think\Collection || $items instanceof \think\Paginator): $i = 0; $__LIST__ = $items;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                                <tr>
                                    <th><input type="checkbox" class="btnSelectItem" name="btSelectItem" id="btSelectItem<?php echo $item['id']; ?>" val="<?php echo $item['id']; ?>" onclick="btSelectItem(<?php echo $item['id']; ?>);"></th>
                                    <!--<th><?php echo $item['id']; ?></th>-->
                                    <th><?php echo $item['project_number']; ?></th>
                                    <th><?php echo $item['project_name']; ?></th>
                                    <th><?php echo $item['QH34']; ?></th>
                                    <th><?php echo $item['QH35']; ?></th>
                                    <th><?php echo $item['Budget']; ?></th>
                                    <th style="text-align: center">
                                        <a href="javascript:;" class="edit" onclick="edit(<?php echo $item['id']; ?>)">编辑</a>
                                        <a href="javascript:;" class="edit" onclick="del(<?php echo $item['id']; ?>)">删除</a></th>
                                </tr>
                                <?php endforeach; endif; else: echo "" ;endif; ?>

                                </tbody>
                            </table>
                        </div>
                        <?php }else{ ?>
                        <hr>
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