<?php

namespace app\common\model;

use think\Model;

class Category extends Model
{
    // 定义关联关系 一个分类下有多个品牌
    public function brands()
    {
        return$this->hasMany('Brand','cate_id','id');
    }
    // 截取器 对pid_path 进行转换
    public function getPidPathAttr($value){
        return $value ?explode('_',$value) : [];
    }
}