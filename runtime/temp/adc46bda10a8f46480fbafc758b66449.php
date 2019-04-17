<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:71:"E:\fadmin\public/../application/index\view\cms\rdsystem\structlist.html";i:1555396029;s:52:"E:\fadmin\application\index\view\layout\default.html";i:1554877745;s:49:"E:\fadmin\application\index\view\common\meta.html";i:1547349021;s:52:"E:\fadmin\application\index\view\common\sidenav.html";i:1553668112;s:51:"E:\fadmin\application\index\view\common\script.html";i:1547349021;}*/ ?>
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

    table {
        border-collapse: collapse;
        margin: 0 auto;
    }

    table, tr, td {
        border: 1px solid black;
    }

    td {
        text-align: center;
        height: 54px; /*这里需要自己调整，根据自己的需求调整高度*/
        min-width: 70px;
        position: relative;
    }
    td[class=first]{
        width: 150px;
        height: 109px;
    }
    td[class=first]:before {
        content: "";
        position: absolute;
        width: 1px;
        height: 159px;
        top: 0;
        left: 0;
        background-color: #000;
        display: block;
        transform: rotate(-70deg);
        transform-origin: top;
        -ms-transform: rotate(-75deg);
        -ms-transform-origin: top;
    }
    td[class=first]:after {
        content: "";
        position: absolute;
        width: 1px;
        height: 132px;
        top: 0;
        left: 0;
        background-color: #000;
        display: block;
        transform: rotate(-35deg);
        transform-origin: top;
        -ms-transform: rotate(-45deg);
        -ms-transform-origin: top;
    }
    .title1{
        position: absolute;
        top: 8px;
        right:3px;
    }
    .title2{
        position: absolute;
        top: 70px;
        right:15px;
    }
    .title3 {
        position: absolute;
        top: 80px;
        left: 2px;
    }
</style>
<script src="/assets/libs/jquery/dist/jquery.js"></script>
<script src="/assets/libs/fastadmin-layer/dist/layer.js"></script>

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
                            <h2>高新技术企业研发费用明细表</h2>
                        </div>

                        <div class="row" style="text-align: center">
                            <h3><span class="title">2013</span>年度研究开发费用结构明细表(万元)
                                <!--<a href="javascript:;" class="btn btn-success btn-export" title="<?php echo __('导出'); ?>" id="btn-export-file" onclick="Export();"><i class="fa fa-download"></i> <?php echo __('导出'); ?></a>-->
                                <!--<a href="javascript:;" class="btn btn-success btn-export" title="<?php echo __('导出'); ?>" id="btn-export-file-hgj" onclick="ExportGj();"><i class="fa fa-download"></i> <?php echo __('费用归集导出'); ?></a>-->
                                <br>
                            年份<select id="Selectyear">
                                <option>2019</option>
                                <option>2018</option>
                                <option selected>2017</option>
                                <option>2016</option>
                            </select>

                            <br>
                            <br>
                            <table style="font-size: 10px;">
                                <thead>
                                <tr id="ttitle">
                                    <td class="first" rowspan="2" id="first">
                                        <span class="title1">研发项目编号</span><br><span class="title2">累积发生额</span><br><span class="title3">科目</span>
                                    </td>
                                    <td rowspan="2" style="width: 80px;" id="total">总计</td>
                                </tr>
                                <tr id="project_name">
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="totalA">内部研究开发费用</td>
                                        <td id="totalAtotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="ryrgfy">其中：人员人工费用</td>
                                        <td id="ryrgfytotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="zjtrfy">             直接投入费用</td>
                                        <td id="zjtrfytotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="zjfy">             折旧费用与长期待摊费用</td>
                                        <td id="zjfytotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="zbfy">             无形资产摊销费用</td>
                                        <td id="zbfytotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="wxzctx">             设计费用</td>
                                        <td id="wxzctxtotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="xcpsjf">             装备调试费用与试验费用</td>
                                        <td id="xcpsjftotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="other">             其他费用</td>
                                        <td id="othertotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="wtyffy">委托外部研究开发费用</td>
                                        <td id="wtyffytotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="jnyffy">   其中：境内的外部研发费用</td>
                                        <td id="jnyffytotal"></td>
                                    </tr>
                                    <tr>
                                        <td id="yftrhj">研究开发费用(内、外部)小计</td>
                                        <td id="yftrhjtotal"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        fetch();
        var type_text = $("#Selectyear").find("option:selected").text();
        $('.title').text(type_text);
        $("#Selectyear").on('change',function () {
            fetch();
            let type_text = $(this).find("option:selected").text();
            $('.title').text(type_text);
        });
        


    });

    function Export(){
        let year = $("#Selectyear").val();
        location.href="/index/cms.rdsystem/exportJgmx?year="+year;
    }

    function ExportGj(){
        let year = $("#Selectyear").val();
        location.href="/index/cms.rdsystem/exportCollect?year="+year;
    }

    function fetch() {
        var year = $("#Selectyear").val();
        $.ajax({
            url:"<?php echo url('index/cms.rdsystem/structlist'); ?>",
            type:'get',
            data : {year:year},
            success : function(data) {
                //empty
                $('#first').nextUntil("#total").remove();
                $('#project_name').empty();
                $('#ryrgfy').nextUntil("#ryrgfytotal").remove();
                $('#zjtrfy').nextUntil("#zjtrfytotal").remove();
                $('#zjfy').nextUntil("#zjfytotal").remove();
                $('#wxzctx').nextUntil("#wxzctxtotal").remove();
                $('#xcpsjf').nextUntil("#xcpsjftotal").remove();
                $('#zbfy').nextUntil("#zbfytotal").remove();
                $('#other').nextUntil("#othertotal").remove();
                $('#wtyffy').nextUntil("#wtyffytotal").remove();
                $('#jnyffy').nextUntil("#jnyffytotal").remove();
                $('#yftrhj').nextUntil("#yftrhjtotal").remove();
                $('#jnyfhj').nextUntil("#jnyfhjtotal").remove();
                $('#totalA').nextUntil("#totalAtotal").remove();
                //project
                var ttitle = '';
                var tproject = '';
                var ryrgfyTD = '';
                var zjtrfyTD = '';
                var zjfyTD = '';
                var wxzctxTD = '';
                var xcpsjfTD = '';
                var zbfyTD = '';
                var otherTD = '';
                var wtyffyTD = '';
                var jnyffyTD = '';
                var yftrhjTD = '';
                var jnyfhjTD = '';
                var totalATD = '';

                var ryrgfytotal = 0;
                var zjtrfytotal = 0;
                var zjfytotal = 0;
                var wxzctxtotal = 0;
                var xcpsjftotal = 0;
                var zbfytotal = 0;
                var othertotal = 0;
                var wtyffytotal = 0;
                var jnyffytotal = 0;
                var yftrhjtotal = 0;
                var jnyfhjtotal = 0;
                var totalAtotal = 0;
                $.each(data, function (i, v) {
                    ttitle += '<td>' + v.project_number + '</td>';
                    tproject += '<td>' + v.project_name + '</td>';
                    ryrgfyTD += '<td>' + v.ryrgfy + '</td>';
                    zjtrfyTD += '<td>' + v.zjtrfy + '</td>';
                    zjfyTD += '<td>' + v.zjfy + '</td>';
                    wxzctxTD += '<td>' + v.wxzctxfy +'</td>';
                    xcpsjfTD += '<td>' + v.xcpsjfy +'</td>';
                    zbfyTD  += '<td>' + v.zbfy +'</td>';
                    otherTD  += '<td>' + v.other +'</td>';
                    wtyffyTD  += '<td>' + v.wtyffy*0.8 +'</td>';
                    jnyffyTD  += '<td>' + v.jnyffy*0.8 +'</td>';
                    totalATD  += '<td>' + v.totalA +'</td>';
                    let yftr_total = parseFloat(v.totalA)+(parseFloat(v.wtyffy)*0.8);
                    let jnyf_total = parseFloat(v.totalA)*0.8+parseFloat(v.jnyffy)*0.8;
                    yftrhjTD  += '<td>' + yftr_total +'</td>';
                    jnyfhjTD  += '<td>' + jnyf_total +'</td>';
                    ryrgfytotal += parseFloat(v.ryrgfy);
                    zjtrfytotal += parseFloat(v.zjtrfy);
                    zjfytotal += parseFloat(v.zjfy);
                    wxzctxtotal += parseFloat(v.wxzctxfy);
                    xcpsjftotal += parseFloat(v.xcpsjfy);
                    zbfytotal += parseFloat(v.zbfy);
                    othertotal += parseFloat(v.other);
                    wtyffytotal += parseFloat(v.wtyffy);
                    jnyffytotal += parseFloat(v.jnyffy);
                    yftrhjtotal += parseFloat(yftr_total);
                    jnyfhjtotal += parseFloat(jnyf_total);
                    totalAtotal += parseFloat(v.totalA);
                });
                //信息列
                $("#total").before(ttitle);
                $("#project_name").append(tproject);
                $("#ryrgfytotal").before(ryrgfyTD);
                $("#zjtrfytotal").before(zjtrfyTD);
                $("#zjfytotal").before(zjfyTD);
                $("#wxzctxtotal").before(wxzctxTD);
                $("#xcpsjftotal").before(xcpsjfTD);
                $("#zbfytotal").before(zbfyTD);
                $("#othertotal").before(otherTD);
                $("#wtyffytotal").before(wtyffyTD);
                $("#jnyffytotal").before(jnyffyTD);
                $("#yftrhjtotal").before(yftrhjTD);
                $("#jnyfhjtotal").before(jnyfhjTD);
                $("#totalAtotal").before(totalATD);
                //合计列
                $("#ryrgfytotal").html(ryrgfytotal.toFixed(2));
                $("#zjtrfytotal").html(zjtrfytotal.toFixed(2));
                $("#zjfytotal").html(zjfytotal.toFixed(2));
                $("#wxzctxtotal").html(wxzctxtotal.toFixed(2));
                $("#xcpsjftotal").html(xcpsjftotal.toFixed(2));
                $("#zbfytotal").html(zbfytotal.toFixed(2));
                $("#othertotal").html(othertotal.toFixed(2));
                $("#wtyffytotal").html(wtyffytotal.toFixed(2));
                $("#jnyffytotal").html(jnyffytotal.toFixed(2));
                $("#yftrhjtotal").html(yftrhjtotal.toFixed(2));
                $("#jnyfhjtotal").html(jnyfhjtotal.toFixed(2));
                $("#totalAtotal").html(totalAtotal.toFixed(2));
            }
        });

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