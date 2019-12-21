<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Collect extends Base {
    public function track(){
        // 从点击以后的那个页面取出商品的id  这样就是获取他点击的商品了
        $id=cookie('ids');
//        dump($id);die;
        //$id=explode(',',$id);
        $id=array_unique($id);
        $goods=\app\common\model\Goods::where('id','in',$id)->order('id desc')->paginate(12);
        $count=\app\common\model\Goods::where('id','in',$id)->count();
//        dump($goods);die;
        // 查询数据表
        return view('footmark',['goods'=>$goods,'count'=>$count]);
    }

    // 单独创建一个方法来实现 点击我的收藏  展示页面
    public function show(){
        $user=session('login_info.id');
        $res=\app\common\model\Collect::where('user_id',$user)->select();
        return view('/collect/collect',['res'=>$res]);
    }
}
