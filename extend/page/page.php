<?php
namespace page;

use think\Paginator;

class Page extends Paginator
{

    //首页
    protected function home() {
        if ($this->currentPage() > 1) {
            //return "<a href='" . $this->url(1) . "' title='首页'>首页</a>";
        } else {
            //return "<p>首页</p>";
        }
    }

    //上一页
    protected function prev() {
        if ($this->currentPage() > 1) {
            return "<li class='page-pre'><a href='" . $this->url($this->currentPage - 1) . "' title='上一页'>上一页</a></li>";
        } else {
            return "<li class='page-first-separator disabled'><a href='javascript:;' class='disabled'>上一页</a></li>";
        }
    }

    //下一页
    protected function next() {
        if ($this->hasMore) {
            return "<li class='page-next'><a href='" . $this->url($this->currentPage + 1) . "' title='下一页'>下一页</a></li>";
        } else {
            return "<li class='page-first-separator disabled'><a href='javascript:;' class='disabled'>下一页</a></li>";
        }
    }

    //尾页
    protected function last() {
        if ($this->hasMore) {
           // return "<a href='" . $this->url($this->lastPage) . "' title='尾页'>尾页</a>";
        } else {
           // return "<p>尾页</p>";
        }
    }

    //统计信息
    protected function info(){
        $listRows = $this->listRows;
        $total = $this->total;
        $url = $this->url(1);
        $info = '<span class="pagination-info">总共 <b>'. $this->total . '</b> 条数据 </span><span class="page-list">每页显示 <span class="btn-group dropup">';
        $info .='                           <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="page-size">'.$listRows.'</span> <span class="caret"></span></button>';
         $info .= '   <ul class="dropdown-menu" role="menu">';
          $info .='                              <li role="menuitem" ><a href="'.$url.'&limit=10">10</a></li>';
           $info .='                             <li role="menuitem"><a href="'.$url.'&limit=25">25</a></li>';
           $info .='                             <li role="menuitem"><a href="'.$url.'&limit=50">50</a></li>';
           $info .='                             <li role="menuitem"><a href="'.$url.'&limit='.$total.'">All</a></li>';
           $info .='                         </ul>';
           $info .='                     </span> 条记录</span><div class="pull-right pagination" style="margin:-17px 0;    position: absolute;
    right: 25px;">';
           $info .='                     <ul class="pagination">';
        return $info;
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $side   = 1;
        $window = $side * 2;

        if ($this->lastPage < $window + 6) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $window) {
            $block['first'] = $this->getUrlRange(1, $window + 2);
            $block['last']  = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $window)) {
            $block['first'] = $this->getUrlRange(1, 2);
            $block['last']  = $this->getUrlRange($this->lastPage - ($window + 2), $this->lastPage);
        } else {
            $block['first']  = $this->getUrlRange(1, 2);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            $block['last']   = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        }

        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<div class="pagination">%s %s %s</div>',
                    $this->prev(),
                    $this->getLinks(),
                    $this->next()
                );
            } else {
                return sprintf(
                    '<div class="fixed-table-pagination" style="display: block;">
                            <div class="pull-left pagination-detail"> %s %s %s %s %s %s</div>',
                    $this->css(),
                    $this->info(),
                    $this->prev(),
                    $this->getLinks(),
                    $this->next(),
                    $this->last()
                );
            }
        }
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
//        return '<a class="page-number" href="' . htmlentities($url) . '" title="第"'. $page .'"页" >' . $page . '</a>';

        return ' <li class="page-number"><a href="' . htmlentities($url) . '">' . $page . '</a></li>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
//        return '<p class="pageEllipsis">' . $text . '</p>';
        return '<li class="page-first-separator disabled"><a href="#">' . $text . '</a></li>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<li class="page-number active"><a href="#" >' . $text . '</a></li>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }

    /**
     * 分页样式
     */
    protected function css(){
        return '<style>
        .pager li{
          margin: 0 0!important;
        }
</style> ';
    }
}
