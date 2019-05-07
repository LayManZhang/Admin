<?php

namespace app\admin\model\cms;

use think\Model;

class Xmjb extends Model
{
    // 表名
    protected $name = 'cms_xmjb';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'grade_type_text'
    ];
    

    
    public function getGradeTypeList()
    {
        return ['1' => __('一级'),'2' => __('二级'),'3' => __('三级'),'4' => __('四级')];
    }     


    public function getGradeTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['grade_type']) ? $data['grade_type'] : '');
        $list = $this->getGradeTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
