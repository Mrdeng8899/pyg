<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Cart extends Base
{
    public function addcart(){
        // 只能是post 请求
        if(request()->isGet()){
            // 如果是 get 请求 直接跳转到首页
            $this->redirect('home/index/index');
        }
        // 接收参数
        $params=input();
        // 定义验证参数
        $rule=[
          'goods_id','require',
          'number','require',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->error($validate);
        }
        // 添加购物数据
        \app\home\logic\CartLogic::addCart($params['goods_id'],$params['spec_goods_id'],$params['number']);
        // 显示成功结果页面
        $goods=\app\home\logic\CartLogic::getGoodsWithGoods($params['goods_id'],$params['spec_goods_id']);
        return view('addcart',['goods'=>$goods]);
    }
    public function changenum(){
        // 接收参数
        $params=input();
        // 定义要检测的参数
        $rule=[
          'id','require',
          'number','require|integer|gt:0',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $res=[
                'code'=>400,
                'msg'=>'参数错误',
            ];
            echo json($res);die;
        }
        // 处理数据
        \app\home\logic\CartLogic::changeNum($params['id'],$params['number']);
        $res=[
            'code'=>200,
            'msg'=>'success',
        ];
        echo json_encode($res);die;

    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    // 购物车列表
    public function index()
    {
//        // 测试 cookie  添加购物车 是否成功
//        $data=cookie('cart');
//        dump($data);die;
//        cookie('cart',null);
        // 查询每条的所有的购物的记录
        $goods=\app\home\logic\CartLogic::getAllCart();
//        dump($goods);die;
//        dump($goods);die;
        // 对每一条的购物记录 查询商品相关的信息 商品信息和sku信息
        foreach($goods as $k=>$v){
//            echo 1;
            $goods[$k]['goods'] = \app\home\logic\CartLogic::getGoodsWithGoods($v['goods_id'], $v['spec_goods_id']);
        }
        unset($v);
//        dump($goods);die;
        return view('index',['goods'=>$goods]);
    }

    public function changestatus(){
        // 接收参数
        $params=input();
        // 参数检测
        $validate=$this->validate($params,[
           'id'=>'require',
           'status'=>'require|in:0,1',
        ]);
        if($validate !==true){
            $res=[
              'code'=>400,
              'msg'=>$validate,
            ];
        return json($res);
        }
        // 处理数据
        \app\home\logic\CartLogic::changeStatus($params['id'],$params['status']);
        // 返回数据
        $res=[
          'code'=>200,
          'msg'=>'success',
        ];
        return json($res);
    }
    public function delcart($id){
        if(empty($id)){
            $res=[
                'code'=>400,
                'msg'=>'参数检测',
            ];
            return json($res);
        }
        // 参数记录 调用封装的方法
        \app\home\logic\CartLogic::delCart($id);
        // 返回 数据
        $res=[
          'code'=>200,
          'msg'=>'success',
        ];
        return json($res);
    }

}
