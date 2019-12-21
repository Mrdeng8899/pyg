<?php
namespace app\home\logic;
class OrderLogic{
    // 查询选中的购物记录以及商品信息
    public static function getCartWithGoods(){
        // 关联模型
        $user_id=session('login_info.id');
        $card_data=\app\common\model\Cart::with('goods,spec_goods')->where('user_id',$user_id)->where('is_selected','1')->select();
        // 使用 sku价格和库存 商品覆盖spu的价格和库存
        $card_data=(new \think\Collection($card_data))->toArray();
        // 累加 总数数量 和价格
        $total_number=0;
        $total_price=0;
        foreach($card_data as $k=>&$v){
            if(!empty($v['spec_goods'])){
                $v['goods']['number']=$v['spec_goods']['store_count'];
                $v['goods']['frozen_number']=$v['spec_goods']['store_frozen'];
                $v['goods']['goods_price']=$v['spec_goods']['price'];
                $v['goods']['cost_price']=$v['spec_goods']['cost_price'];
            }
        // 累加
        $total_number +=$v['number'];
        $total_price +=$v['number'] * $v['goods']['goods_price'];
        }
        $res=[
          'data'=>$card_data,
          'total_price'=>$total_price,
          'total_number'=>$total_number,
        ];
        return $res;
    }
}