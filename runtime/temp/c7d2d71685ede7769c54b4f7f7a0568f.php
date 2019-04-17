<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:71:"E:\fadmin\public/../application/index\view\cms\rdsystem\costdetail.html";i:1555379341;s:52:"E:\fadmin\application\index\view\layout\default.html";i:1554877745;s:49:"E:\fadmin\application\index\view\common\meta.html";i:1547349021;s:52:"E:\fadmin\application\index\view\common\sidenav.html";i:1553668112;s:51:"E:\fadmin\application\index\view\common\script.html";i:1547349021;}*/ ?>
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
    .table thead tr td{
        vertical-align: middle!important;
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
                            <h3>高新技术企业研发费用明细表-<span class="title">工资薪金</span></h3>
                            年份<select id="Selectyear">
                                <option>2019</option>
                                <option>2018</option>
                                <option selected>2017</option>
                                <option>2016</option>
                            </select>
                            费用类别<select id="Selecttype">
                                <option value="" >请选择</option>
                                <option value="a1" disabled>人员人工费用</option>
                                <option value="11" selected> ├工资薪金</option>
                                <option value="12" > ├五险一金</option>
                                <option value="13" > └外聘研发人员的劳务费</option>
                                <option value="a2" disabled>直接投入费用</option>
                                <option value="21" > ├材料费</option>
                                <option value="22" > ├燃料费</option>
                                <option value="23" > ├动力费用</option>
                                <option value="24" > ├试制模具、工艺装备费</option>
                                <option value="25" > ├样品、样机费等</option>
                                <option value="26" > ├试制产品检验费</option>
                                <option value="27" > ├仪器设备运维、检验费等</option>
                                <option value="28" > └仪器设备租赁费</option>
                                <option value="a3" disabled>折旧费用与长期待摊费用</option>
                                <option value="31" > ├仪器折旧</option>
                                <option value="32" > └设备折旧</option>
                                <option value="33" > └在用建筑物折旧</option>
                                <option value="34" > └长期待摊费用</option>
                                <option value="a4" disabled>无形资产摊销</option>
                                <option value="41" > ├软件摊销</option>
                                <option value="42" > ├专利权摊销</option>
                                <option value="43" > └非专利技术摊销</option>
                                <option value="a5" disabled>新产品设计费等</option>
                                <option value="51" > └新产品设计费</option>
                                <option value="52" > └新工艺规程制定费</option>
                                <option value="53" > └新药研制的临床试验费</option>
                                <option value="54" > └勘探开发技术的现场试验费</option>
                                <option value="a6" disabled>装备调试费用与试验费用</option>
                                <option value="61" > └装备调试费用</option>
                                <option value="62" > └田间试验费</option>
                                <option value="a7" disabled>其他相关费用</option>
                                <option value="71" > ├技术图书资料费、资料翻译费、专家咨询费、高新科技研发保险费	</option>
                                <option value="72" > ├研发成果的检索、分析、评议、论证、鉴定、评审、评估、验收费用	</option>
                                <option value="73" > ├知识产权的申请费、注册费、代理费	</option>
                                <option value="74" > └差旅费、会议费</option>
                                <option value="75" > ├职工福利费、补充养老保险费、补充医疗保险费	</option>
                                <option value="76" > └通讯费</option>
                                <option value="a8" disabled>委托外部研究开发投入</option>
                                <option value="81" > ├委托境内研发	</option>
                                <option value="82" > ├委托境外研发	</option>
                            </select>
                            <br>
                            <br>
                            <table border="1" style="border-collapse:collapse"  class="table table-bordered table-hover" lay-even lay-skin="nob">
                                <thead>
                                <tr id="ttitle">
                                    <td rowspan="3">日期</td>
                                    <td rowspan="3">凭证号</td>
                                    <td rowspan="3">摘 要</td>
                                    <td rowspan="3">科 目</td>
                                    <td rowspan="3">凭证金额	</td>
                                    <td rowspan="3">计入研发费用金额(元)</td>
                                </tr>
                                <tr id="tproject"></tr>
                                <tr id="ttime"></tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="fixed-table-pagination" style="display: block;">
                        <div class="pull-left pagination-detail">
                        <span class="pagination-info">总共 <span id="total_row"></span> 条记录</span>
                        <span class="page-list">每页显示 <span class="btn-group dropup">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="page-size">10</span> <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                        <li class="size" role="menuitem"><a href="javascript:;">10</a></li>
                        <li class="size" role="menuitem"><a href="javascript:;">25</a></li>
                        <li class="size" role="menuitem"><a href="javascript:;">50</a></li>
                        <li class="size" role="menuitem"><a href="javascript:;">All</a></li>
                        </ul>
                        </span> 条记录</span>
                        </div>
                        <div class="pull-right pagination" style="margin:-17px 0;">
                        <ul class="pagination">
                        <li class="page-pre"><a href="#">上一页</a></li>
                        <li class="page-next"><a href="#">下一页</a></li>
                        </ul>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        fetch();
        var type_text = $("#Selecttype").find("option:selected").text();
        type_texts = type_text.substr(2);
        $('.title').text(type_texts);
        $("#Selecttype").on('change',function () {
            fetch();
            let type_text = $(this).find("option:selected").text();
            type_texts = type_text.substr(2);
            $('.title').text(type_texts);
        });
        $("#Selectyear").on('change',function () {
            fetch();
        });

    });


   function prompt(e) {
        layer.prompt({title: '请输入金额', formType: 3,value:$(e).prev('input').val()}, function(text, index){
            var type = $("#Selecttype").find("option:selected").val();
            var rid = $(e).attr('rid');
            var pid = $(e).attr('pid');
            var Debit_amount = $(e).attr('Debit_amount');
            var amount = text;
            $(e).prev('input').val(text);
            var inputall = $(e).parent().parent().parent('tr').children('td').find('input');//获取所有input框的值 求和
            var total = 0;
            $.each(inputall,function (i,v) {
               var cost = $(this).val();
               if(cost){
                   total += parseFloat(cost);
               }

            });
            $(e).parent().parent().parent('tr').children('td#preamount').text(total);
            $.ajax({
                url:"<?php echo url('index/cms.rdsystem/editcostdetil'); ?>",
                type:'post',
                data : {rid:rid,pid:pid,type:type,amount:amount},
                success : function(data){
                        layer.msg(data.msg,{time:1000})
                        $(e).attr('rate',text);
                }
            });
            layer.close(index);
        });
    };

   //渲染数据
    function fetch(page=1,limit=10) {
        var year = $("#Selectyear").val();
        var type = $("#Selecttype").val();
        var start = (page-1)*limit;
        var end = page*limit;
        $.ajax({
            url:"<?php echo url('index/cms.rdsystem/costdetail'); ?>",
            type:'post',
            data : {year:year,type:type},
            success : function(data){

                //empty
                $("#ttitle td:gt(5)").remove();
                $("#tproject td").remove();
                $("#ttime td").remove();

                //project
                var ttitle = '';
                var tproject = '';
                var ttime = '';
                var total_row = data['data'].length;
                total_page = Math.ceil(total_row/limit);
                $("#total_row").text(total_row);
                $.each(data['project'],function (i,v) {
                    ttitle += '<td>'+v.project_number+'</td>'
                    tproject += '<td>'+v.project_name+'</td>'
                    ttime += '<td>'+v.QH34+'-'+v.QH35+'</td>'
                });
                $("#ttitle").append(ttitle);
                $("#tproject").append(tproject);
                $("#ttime").append(ttime);
                var tbody = '';

                $.each(data['data'],function (index,val) {
                    if(start<=index&&index<end){
                    let total = 0;
                    tbodyp = '';
                    $.each(data['project'],function (i,v) {

                        let pid = v.id;
                        if(val[pid]){
                            if(val[pid]){
                                var amount = parseFloat(val[pid].amount);
                            }else{
                                var amount = parseFloat('0.00');
                            }

                            if(val[pid].rate!==null){
                                var rate = parseFloat(val[pid].rate)*100;
                            }else{
                                var rate = '';
                            }
                        }else{
                            var amount = parseFloat('0.00');
                            var rate = '';
                        }
                        total += amount;

                        let pzje = parseInt(val.Debit_amount)==0 ? '-'+val.Credit_amount : val.Debit_amount;
                        tbodyp += '<td><div class="input-group ">' +
                            '                                                <input type="text" class="form-control showInput" value="'+amount+'" readonly="readonly">' +
                            '                                                <span class="input-group-addon btn-success go-edit" id="prompt" rid="'+val.id+'" pid="'+v.id+'" Debit_amount="'+pzje+'" rate="'+rate+'" onclick="prompt(this)" style="background-color: #5cb85c;border-color: #5cb85c">' +
                            '                                                    <i class="fa fa-edit"></i>' +
                            '                                                </span>' +
                            '                                            </div></td>'
                    });
                    total = decimal(total,2);
                    let pzje = parseInt(val.Debit_amount)==0 ? '-'+val.Credit_amount : val.Debit_amount;
                    tbody += '<tr val="'+val.id+'">' +
                        '                                    <td>'+val.Date+'</td>' +
                        '                                    <td>'+val.Document_number+'</td>' +
                        '                                    <td>'+val.Abstract+'</td>' +
                        '                                    <td>'+val.Acct_Tit+'</td>' +
                        '                                    <td id="pzje">'+pzje+'</td>' +
                        '                                    <td id="preamount">'+total+'</td>' ;
                    tbody += tbodyp;
                    tbody += '                                </tr>';
                    }
                });
                $(".table tbody tr").remove();
                $(".table tbody").append(tbody);

                pages(total_page,page)
            }
        });
    }

    //渲染分页
    function pages(total_page,current_page) {
        $('.page-pre').nextUntil(".page-next").remove();
        var page = '';
        for (let i=1;i<=total_page;i++){
            if(current_page==i){
                page += '<li class="page-number active" disabled><a href="javascript:;">'+i+'</a></li>';
            }else{
                page += '<li class="page-number" onclick="page(this)"><a href="javascript:;">'+i+'</a></li>';
            }
        }
        $(".page-next").before(page);
    }

    //跳页
    function page(e){
        var page = $(e).text();
        fetch(page)
    };

    //每页展示条数
    $(".size").on('click',function () {
        var size = $(this).text();
        var page_size = size=='All' ? 10000 : size;
        $(".page-size").text(size);
        fetch(1,page_size);
    });

    //上一页
    $(".page-pre").on('click',function () {
        var curren_page = $('.page-number.active').text();
        var page = curren_page-1<1 ? 1 : curren_page-1;
        fetch(page)
    });

    //下一页
    $(".page-next").on('click',function () {
        var curren_page = $('.page-number.active').text();
        var page = parseInt(curren_page)+1 > total_page ? curren_page : parseInt(curren_page)+1;
        fetch(page)
    });



    function decimal(num,v){
        var vv = Math.pow(10,v);
        return Math.round(num*vv)/vv;
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