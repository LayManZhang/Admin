<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:54:"E:\fadmin\addons\cms\view\hook\user_sidenav_after.html";i:1555395762;}*/ ?>
<ul class="list-group">
    <li class="list-group-heading"><?php echo __('内容管理'); ?></li>
    <!--如果需要直接跳转对应的模型(比如我的新闻,我的产品,发布新闻,发布产品)，可以在链接后加上 ?model_id=模型ID -->
    <li class="list-group-item <?php echo $actionname=='my'?'active':''; ?>"><a href="<?php echo url('index/cms.archives/my'); ?>"><i class="fa fa-list fa-fw"></i> <?php echo __('我发布的文章'); ?></a></li>
    <li class="list-group-item <?php echo $actionname=='post'?'active':''; ?>"><a href="<?php echo url('index/cms.archives/post'); ?>"><i class="fa fa-pencil fa-fw"></i> <?php echo __('发布文章'); ?></a></li>
</ul>
<ul class="list-group">
    <li class="list-group-heading"><?php echo __('项目评估'); ?></li>
    <!--如果需要直接跳转对应的模型(比如我的新闻,我的产品,发布新闻,发布产品)，可以在链接后加上 ?model_id=模型ID -->
    <li class="list-group-item <?php echo $actionname=='assessment_list'?'active':''; ?>"><a href="<?php echo url('index/cms.diyform/assessment_list'); ?>"><i class="fa fa-list fa-fw"></i> <?php echo __('我的项目评估'); ?></a></li>
</ul>
<ul class="list-group">
    <li class="list-group-heading"><?php echo __('研发辅助账系统'); ?></li>
    <!--如果需要直接跳转对应的模型(比如我的新闻,我的产品,发布新闻,发布产品)，可以在链接后加上 ?model_id=模型ID -->
    <li class="list-group-item <?php echo $actionname=='research'?'active':''; ?>"><a href="<?php echo url('index/cms.rdsystem/research'); ?>"><i class="fa fa-bookmark-o fa-fw"></i> <?php echo __('明细账'); ?></a></li>
    <li class="list-group-item <?php echo $actionname=='project'?'active':''; ?>"><a href="<?php echo url('index/cms.rdsystem/project'); ?>"><i class="fa fa-bookmark fa-fw"></i> <?php echo __('研发项目情况'); ?></a></li>
    <li class="list-group-item <?php echo $actionname=='costdetail'?'active':''; ?>"><a href="<?php echo url('index/cms.rdsystem/costdetail'); ?>"><i class="fa fa-tasks fa-fw"></i> <?php echo __('研发费用明细'); ?></a></li>
    <li class="list-group-item <?php echo $actionname=='structlist'?'active':''; ?>"><a href="<?php echo url('index/cms.rdsystem/structlist'); ?>"><i class="fa fa-table fa-fw"></i> <?php echo __('结构明细表'); ?></a></li>
    <li class="list-group-item <?php echo $actionname=='reporting'?'active':''; ?>"><a href="<?php echo url('index/cms.rdsystem/reporting'); ?>"><i class="fa fa-table fa-fw"></i> <?php echo __('报表导出'); ?></a></li>

</ul>
