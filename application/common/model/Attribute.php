<?php

namespace app\common\model;

use think\Model;

class Attribute extends Model
{
    // 定义 获取器 对attr_values 字段值进行处理
    // 获取器方法名称get开头attr结尾 中间是字段名称
    public function getAttrValuesAttr($value){
        // 将$value值 用逗号隔开 分隔成数组
        return $value ? explode(',',$value) : [];
    }
}
