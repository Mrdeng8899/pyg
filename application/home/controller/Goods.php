<?php

namespace app\home\controller;

use think\Controller;

class goods extends Base
{
    //
    public function index($id=0)
    {
//        $this->view->engine->layout(false);
        //接收参数
        $keywords = input('keywords');
        if(empty($keywords)){
            //获取指定分类下商品列表
            if(!preg_match('/^\d+$/', $id)){
                $this->error('参数错误');
            }
            //查询分类下的商品
            $list = \app\common\model\Goods::where('cate_id', $id)->order('id desc')->paginate(10);
            //查询分类名称
            $category_info = \app\common\model\Category::find($id);
            $cate_name = $category_info['cate_name'];
        }else{
            try{
                //从ES中搜索
                $list = \app\home\logic\GoodsLogic::search();
                $cate_name = $keywords;
            }catch (\Exception $e){
                $this->error('服务器异常');
            }
        }
        return view('index', ['list' => $list, 'cate_name' => $cate_name]);
    }
    public function detail($id){
        // 查询商品数据  做 缩略图 查询商品下的所有的sku规格商品sepc_goods
        $goods=\app\common\model\Goods::with('GoodsImages,spec_goods')->find($id);
        // 设置他的第一个 选择的默认价格
        if($goods['spec_goods']){
            $goods['goods_price']=$goods['spec_goods'][0]['price'];
        }
        // 从spec_goods取出所有相关的规格值id
        $value_ids=array_column($goods['spec_goods'],'value_ids');       // ['28_32', '28_33', '29_32', '29_33']
        $value_ids=array_unique(explode('_',implode('_',$value_ids)));

        //  炸开的结果是
        //implode 28_32_28_33_29_32_29_33
        //explode ['28', '32', '28', '33', '29', '32', '29','33']
        //array_unique ['28', '32', '33', '29']

        // 查询规格值表连表规格名称表
        $spec_values=\app\common\model\SpecValue::with('spec_bind')->where('id','in',$value_ids)->select();
        $specs=[];
        foreach($spec_values as $k=>$v){
            $specs[$v['spec_id']]=[
                'id'=>$v['spec_id'],
                'spec_name'=>$v['spec_name'],
                'spec_value'=>[]
            ];
        }
        // 组装规格值
        foreach($spec_values as $k=>$v){
            $specs[$v['spec_id']]['spec_values'][]=[
              'id'=>$v['id'],
              'spec_value'=>$v['spec_value'],
            ];
        }
        //  价格切换的显示  数据的转换
        $values_msp=[];
        foreach($goods['spec_goods'] as $v){
            $values_msp[$v['value_ids']]=[
              'id'=>$v['id'],
               'price'=>$v['price'],
            ];
        }
        // 将数据 放在js使用 需要转换成 json格式的
        $values_msp=json_encode($values_msp);
//        cookie('id',null);die;
        $data=cookie('ids') ?:[];
//        dump($data);die;
        $data[]=$id;
        cookie('ids',$data,7*86400);

//         Cookie('id',null);
//        $save_id = Cookie("id")?Cookie("id"):[];
//        // dump($save_id);die;
//        $save_id[]=$id;
//        $save_id=json_encode($save_id);
//        Cookie("id",$save_id,3600*7);



        return view('detail',['goods'=>$goods,'specs'=>$specs,'values_msp'=>$values_msp]);
    }
        // 查询goods里面的数据
    public function edit($id){
        // 查看你点击的是点击的那个商品信息
        $user=session('login_info.id');
        if(empty($user)){
            $this->error("请您重新登入一下");
        }
//        dump($id);die;
        // 根据这个id  查询商品表的所有的信息
        $data=\app\common\model\Goods::field('goods_name,goods_logo,goods_price')->find($id);
        if(empty($data)){
            $this->error("这个商品可能已经删除,你不能删除此信息");
        }
        $data=$data->toArray();
        $data['user_id']=$user;
//        dump($data);die;
        // 查询出来添加到 collect表里面去
        $create=\app\common\model\Collect::create($data,true);
        if(empty($create)){
            $this->error("请添加有效的商品信息");
        }
//        dump($res);die;
        // 展示数据
        $res=\app\common\model\Collect::where('user_id',$user)->order('id desc')->paginate(10);
        return view('/collect/collect',['res'=>$res]);
    }
}
