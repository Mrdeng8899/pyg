<?php

namespace app\common\model;

use think\Model;

class Brand extends Model
{
    // 定义关联关系 id cate_id 一个品牌属于一个分类
    public function category(){
        return $this->belongsTo('Category','cate_id','id');
    }
    public function categoryBind(){
        return $this->belongsTo('Category','cate_id','id')->bind('cate_name,pid');
    }

}
