<?php

namespace app\admin\model\cms;

use think\Model;

class Addonxmgsk extends Model
{
    // 表名
    protected $name = 'cms_addonxmgsk';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'year_text'
    ];
    

    
    public function getYearList()
    {
        return ['2013' => __('Year 2013'),'2014' => __('Year 2014'),'2015' => __('Year 2015'),'2016' => __('Year 2016'),'2017' => __('Year 2017'),'2018' => __('Year 2018'),'2019' => __('Year 2019'),'2020' => __('Year 2020')];
    }     


    public function getYearTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['year']) ? $data['year'] : '');
        $list = $this->getYearList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
