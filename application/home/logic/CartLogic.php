<?php
 namespace app\home\logic;
 class CartLogic{
     // 加入购物车
    public static function addCart($goods_id,$spec_goods_id,$number,$is_selected=1){
        // 判断是否 是登入状态 已经登入 就添加到数据表 没有登入添加到cookie
        if(session('?login_info')){
            // 已经登入的 添加到数据表
            $user_id=session('login_info.id');
            // 判断 是否存在相同的购物记录 如果用户id相同 商品id相同 sku的id相同
            $where=[
              'user_id'=>$user_id,
              'goods_id'=>$goods_id,
              'spec_goods_id'=>$spec_goods_id,
            ];
                $info=\app\common\model\Cart::where($where)->find();
                if($info){
                    // 存在相同记录 累加购买数量
                    $info->number +=$number;
                    $info->is_selected=$is_selected;
                    $info->save();
                }else{
                    // 不存在 添加新记录
                    $where['number']=$number;
                    $where['is_selected']=$is_selected;
                    \app\common\model\Cart::create($where,true);
                }
        }else{
            // 为登入 添加到cookie里面
            // 取出已有的数据
            $data=cookie('cart') ?:[];
            // 拼接当前数据的下标
            $key=$goods_id.'_'.$spec_goods_id;
            // 判断是否存在相同记录
            if(isset($data[$key])){
                // 存在 累加数量
                $data[$key]['number'] +=$number;
                $data[$key]['is_selected']=$is_selected;
            }else{
                // 不存在则添加新记录
                $data[$key]=[
                    'id'=>$key,
                    'goods_id'=>$goods_id,
                    'spec_goods_id'=>$spec_goods_id,
                    'is_selected'=>$is_selected,
                    'number'=>$number,
                ];
            }
            // 重新保存数据到cookie
            cookie('cart',$data,7*86400);
        }
    }
    public static function getGoodsWithGoods($goods_id,$spec_goods_id=''){
//        echo 11;die;
        // 如果有$sepc_goods_id 就根据 $spenc_goods_id 查询指定的sku记录
        if($spec_goods_id){
            $where['t2.id']=$spec_goods_id;
        }else{
            // 如果没有 $spec_goods_id 就根据$goods_id 查询指定的商品记录
            $where['t1.id']=$goods_id;
        }
        $goods=\app\common\model\Goods::alias('t1')
            ->join('spec_goods t2','t1.id=t2.goods_id','left')
            ->field('t1.*,t2.id as spec_goods_id,t2.value_ids,t2.value_names,t2.price,t2.cost_price as cost_prices,t2.store_count,t2.store_frozen')
            ->where($where)
            ->find();
        if(!$goods) return[];
        // 如果sku 有记录有值 如果sku的值覆盖 商品的值 价格库存
        if($goods['price'] > 0){
            $goods['goods_price']=$goods['price'];
        }
        if($goods['cost_prices']){
            $goods['cost_price']=$goods['cost_prices'];
        }
        // 库存
        if($goods['store_count']){
            $goods['goods_number']=$goods['store_count'];
        }
        // 冻结库存
        if($goods['store_frozen']){
            $goods['frozen_number']=$goods['store_frozen'];
        }
        return $goods->toArray();
    }
    // 查询所有购物记录
     public static function getAllCart(){
        // 判断登入状态所有购物记录
         if(session('?login_info')){
             //已经登入的
             $user_id=session('login_info.id');
             $data=\app\common\model\Cart::field('id,goods_id,spec_goods_id,number,is_selected')->where('user_id',$user_id)->select();
             // 转换成二维数组
             $data=(new \think\Collection($data))->toArray();
         }else{
             // 没有登入的 取出cookie
             $data=cookie('cart') ?:[];
             // 转换成  数组 和查询数据的是的一样的数组格式 取出外层的下标
             $data=array_values($data);
//             dump($data);
         }
         return $data;
     }

     // 登入后将cookie 购物车迁移到数据表
     public static function cookieToDb()
     {
         // 从 cookie 中获取所有数据
         $data=cookie('cart')?:[];
         // 将数据添加 修改到数据表
         foreach($data as $v){
             self::addCart($v['goods_id'],$v['spec_goods_id'],$v['number']);
         }
         // 删除cookie购物车数据 清除cookie 里面的数据
         cookie('cart',null);
     }
    public static function changeNum($id,$number){
        // 判断 登入状态 已经登入修改数据表  没有登入 修改cookie
        if(session('?login_info')){
            $user_id=session('login_info.id');
            \app\common\model\Cart::update(['number'=>$number],['id'=>$id,'user_id'=>$user_id],true);
        }else{
            // 没有登入   修改cookie数据
            $data=cookie('cart') ?:[];
            // 修改数量
            $data[$id]['number']=$number;
            // 重新保存
            cookie('cart',$data,86400*7);
        }
    }
    public static function changeStatus($id,$is_selected){
        // 判断登录状态
        if(session('?login_info')){
            // 登入 修改数据表
            $user_id=session('login_info.id');
            $where['user_id']=$user_id;
            if($id !='all'){
                // 修改一条
                $where['id']=$id;
            }
            // 修改数据
            \app\common\model\Cart::update(['is_selected'=>$is_selected],$where,true);
        }else{
            // 修改cookie
            $data=cookie('cart') ? :[];
            if($data !='all'){
                // 修改一条
                $data[$id]['is_selected']=$is_selected;
            }else{
                // 修改所有
                foreach($data as $k=>$v){
                    $data[$k]['is_selected']=$is_selected;
                }
            }
            // 重新保存
            cookie('cart',$data,86400*7);
        }
    }
    public static function delcart($id){
        // 判断登录 状态
        if(session('?login_info')){
            // 从数据库表删除
            $user_id=session('login_info.id');
            \app\common\model\Cart::destroy(['id'=>$id,'user_id'=>$user_id]);
        }else{
            // 从 cookie 删除
            $data=cookie('cart')?:[];
            unset($data[$id]);
            cookie('cart',$data,86400*7);
        }
    }
 }