<?php

namespace app\common\model;

use think\Model;

class Cart extends Model
{
    // 定义购物车 商品关联 主键id 商品表 一个购物记录记录一个商品
    public function goods(){
        return $this->belongsTo('Goods','goods_id','id');
    }
    public function specGoods(){
        return $this->belongsTo('SpecGoods','spec_goods_id','id');
    }
}
