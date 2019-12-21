<?php

namespace app\common\model;

use think\Model;

class order extends Model
{
    //
    public function user(){
        return $this->belongsTo('User');
    }
    // 关联模型
    public function orderGoods(){
        return $this->belongsTo('order_Goods','id','order_id');
    }
}
