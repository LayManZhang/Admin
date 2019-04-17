<?php

namespace addons\cms\controller;

use addons\cms\model\Archives;
use addons\cms\model\Channel as ChannelModel;
use addons\cms\model\Modelx;
use think\Config;

/**
 * 栏目控制器
 * Class Channel
 * @package addons\cms\controller
 */
class Channel extends Base
{

    public function index()
    {
        $diyname = $this->request->param('diyname');
        if ($diyname && !is_numeric($diyname)) {
            $channel = ChannelModel::getByDiyname($diyname);
        } else {
            $id = $diyname ? $diyname : $this->request->param('id', '');
            $channel = ChannelModel::get($id);
        }
        if (!$channel) {
            $this->error(__('No specified channel found'));
        }

        $filterlist = [];
        $orderlist = [];

        $filter = $this->request->get('filter/a', []);
        $orderby = $this->request->get('orderby', '');
        $orderway = $this->request->get('orderway', '', 'strtolower');
        $czms = $this->request->get('czms');
        $jqcz = $this->request->get('jqcz');
        $params = ['filter' => '', 'id' => $channel->id, 'diyname' => $channel->diyname];
        if ($filter)
            $params['filter'] = $filter;
        if ($orderby)
            $params['orderby'] = $orderby;
        if ($orderway)
            $params['orderway'] = $orderway;
        if ($channel['type'] === 'link') {
            $this->redirect($channel['outlink']);
        }

        $model = Modelx::get($channel['model_id']);
        if (!$model) {
            $this->error(__('No specified model found'));
        }
        $fields = [];
        foreach ($model->fields_list as $k => $v) {
            if (!$v['isfilter'] || !in_array($v['type'], ['select', 'selects', 'checkbox', 'radio', 'array']) || !$v['content_list'])
                continue;
            $fields[] = [
                'name' => $v['name'], 'title' => $v['title'], 'content' => $v['content_list']
            ];
        }

        $filters = array_intersect_key($filter, array_flip(array_column($fields, 'name')));
        foreach ($fields as $k => $v) {
            $content = [];
            $all = ['' => __('All')] + $v['content'];
            foreach ($all as $m => $n) {
                $active = ($m === '' && !isset($filters[$v['name']])) || (isset($filters[$v['name']]) && $filters[$v['name']] == $m) ? TRUE : FALSE;
                $prepare = $m === '' ? array_diff_key($filters, [$v['name'] => $m]) : array_merge($filters, [$v['name'] => $m]);
                $url = '?' . http_build_query(array_merge(['filter' => $prepare], array_diff_key($params, ['filter' => ''])));
                $content[] = ['value' => $m, 'title' => $n, 'active' => $active, 'url' => $url];
            }
            $filterlist[] = [
                'name'    => $v['name'],
                'title'   => $v['title'],
                'content' => $content,
            ];
        }
        $search = '';
        //$filter['xmjb']  = 108;
        if(isset($filter[0])&&$filter[0]!==''){
            var_dump(123);
            if($czms) {
                foreach ($filter as $k => $v) {
                    $res = '';
                    $searchj = '';
                    $searchm = '';
                    if ($jqcz == $k) {//精确查找的字段
                        if (empty($searchj)) {
                            $searchj = $k . ' =' . '"' . $v . '"';
                        } else {
                            $searchj .= '`' . $k . '`' . ' = ' . '"' . $v . '"';
                        }
                        $res .= $searchj;
                    } else{//模糊查找
                        $str = explode(',', $v);
                        foreach ($str as $key => $item) {
                            if (empty($searchm)) {
                                $searchm .= '(' . 'FIND_IN_SET('.$item.','.$k.')';//将字段切割后进行匹配
                            } else {
                                $searchm .= ' OR ' . 'FIND_IN_SET('.$item.','.$k.')';
                            }
                        }
                        $search .= $searchm.') AND';
                        $res .= $search;
                        $res = substr($res,0,-4);
                    }
                    $searchss = $res;
                }
                $wheres = $searchss;
            }else{
                var_dump(456);
                $res = '';
                foreach ($filter as $k => $v) {
                    $searchj = '';
                    if (empty($searchj)) {
                        //if ($k=='xmjb') {//项目级别
                        $searchj = ' FIND_IN_SET('.$v.','.$k.') ';

                        //}else{
                        //   $searchj = $k . ' =' . '"' . $v . '"';
                        //}
                    } else {
                        //if ($k=='xmjb') {//项目级别
                        $searchj .= ' FIND_IN_SET('.$v.','.$k.') ';

                        //}else {
                        //   $searchj .= '`' . $k . '`' . ' = ' . '"' . $v . '"';
                        //}
                    }
                    $res .= $searchj. ' AND ';

                }
                $searchss = substr($res,0,-4);
                $wheres = $searchss;
            }
        }else{
            $wheres = '';
        }


        $sortrank = [
            ['name' => 'default', 'field' => 'weigh', 'title' => __('Default')],
            ['name' => 'views', 'field' => 'views', 'title' => __('Views')],
            ['name' => 'id', 'field' => 'id', 'title' => __('Post date')],
        ];

        $orderby = $orderby && in_array($orderby, ['default', 'id', 'views']) ? $orderby : 'default';
        $orderway = $orderway ? $orderway : 'desc';
        foreach ($sortrank as $k => $v) {
            $url = '?' . http_build_query(array_merge($params, ['orderby' => $v['name'], 'orderway' => ($orderway == 'desc' ? 'asc' : 'desc')]));
            $v['active'] = $orderby == $v['name'] ? true : false;
            $v['orderby'] = $orderway;
            $v['url'] = $url;
            $orderlist[] = $v;
        }
        $orderby = $orderby == 'default' ? 'weigh' : $orderby;
        $pagelist = Archives::alias('a')
            ->where('status', 'normal')
            ->where('deletetime', 'exp', \think\Db::raw('IS NULL'))
            ->where($wheres)
            ->join($model['table'] . ' n', 'a.id=n.id', 'LEFT')
            ->field('a.*')
            ->field('id,content', true, config('database.prefix') . $model['table'], 'n')
            ->where('channel_id', $channel['id'])
            ->order($orderby, $orderway)
            ->paginate($channel['pagesize'], false, ['type' => '\\addons\\cms\\library\\Bootstrap']);
        $pagelist->appends($params);
        if ($this->request->isAjax()) {
            return $pagelist;
        }
        $this->view->assign("__FILTERLIST__", $filterlist);
        $this->view->assign("__ORDERLIST__", $orderlist);
        $this->view->assign("__PAGELIST__", $pagelist);
        $this->view->assign("__CHANNEL__", $channel);
        Config::set('cms.title', $channel['name']);
        Config::set('cms.keywords', $channel['keywords']);
        Config::set('cms.description', $channel['description']);
        $template = preg_replace('/\.html$/', '', $channel["{$channel['type']}tpl"]);
        return $this->view->fetch('/' . $template);
    }


    public function getslbm(){
        $id = $this->request->get('id');
        $data = db('cms_xmjb')->where('parent_id',$id)->field(['id','grade_name as name'])->select();
        return json($data);
    }

}
