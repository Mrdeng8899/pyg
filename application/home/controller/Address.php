<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Address extends Base {

    // 地址修改页面
    public function address(){
        $id=session('login_info.id');
        $data=\app\common\model\Address::where('user_id',$id)->select();
        $info=\app\common\model\User::where('id',$id)->find();
//        dump($data);die;
        return view('address',['data'=>$data,'info'=>$info]);
    }
//    public function edit($id){
//        $id=session('login_info.id');
////        dump($id);
//        $data=\app\common\model\Address::where('user_id',$id)->find();
//        $data=$data->toArray();
////        dump($data);
////        return view();
//    }
    // 地址的新增
    // 地址修改点击确定的时候 正式修改页面
    public function updates(){
        // 取出session的id  绑定用户 的user_id
        $user=session('login_info.id');
//        dump($user);die;
        $params=input();
//        dump($params);die;
        // 组装 city shi qu
        //  添加
        $params['area']=$params['sh'].'_'.$params['shi'].'_'.$params['qu'];
//        dump($params);die;

        $rule=[
          'consignee|收货人'=>'require',
          'address|详细地址'=>'require',
          'phone|手机号码'=>'require',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $res=[
              'code'=>400,
              'msg'=>$validate,
            ];
            return json($res);
        }
        // 判断是不是一个正确的手机号码
        if(empty($params['phone']) && !preg_match("/^1[34578]{1}\d{9}$/",$params['phone'])){
            $res=[
                'code'=>400,
                'msg'=>"请检查您的手机号是否为空",
            ];
            return json($res);
        }
        // 判断是不是一个正确的邮箱格式
        if(empty($params['email']) && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$params['email'])){
            $res=[
                'code'=>400,
                'msg'=>"请您检查一下你的邮箱格式是否正确",
            ];
            return json($res);
        }
        // 判断所在地址是不是为空
        if(empty($params['site'])){
            $res=[
              'code'=>400,
              'msg'=>"请输入正确的地址",
            ];
            return json($res);
        }
        // 添加到数据表里面去
//        $params['area']=$params['site'];
        // 组装用户的id 添加到数据表
        $params['user_id']=$user;
        $res=\app\common\model\Address::create($params,true);
//        dump($res);die;
        if($res){
            $res=[
              'code'=>200,
              'msg'=>"添加成功",
            ];
            return json($res);
        }
    }
    // 地址删除界面
    public function delete($id){
        // 根据用户id 删除 删除这条记录信息  查询数据
        \app\common\model\Address::destroy($id);
        $this->success("删除成功");
    }

    // 编辑  暂时页面的
    public function edit(){
//        $params=input('data');
        $params=request()->get();
        foreach ($params as $k=>$v){
            $id=$k;
        }
//        dump($id);die;
        // 根据 循环出来的id 查询 数据表
        $data=\app\common\model\Address::find($id);
        // 把这个id记录到缓存里面
        cache('update_id',$id);
        $data['area']=explode('_',$data['area']);
//        dump($data);
        $res=[
            'code'=>200,
            'data'=>$data,
        ];
        return json($res);
    }

    // 保存数据 保存到数据表 编辑 修改
    public function save(){
        $user=session('login_info.id');
        $params=input();
//        dump($params);die;
        // 参数检测
        $rule=[
          'site|地区'=>'require',
          'consignee|姓名'=>'require',
          'address|详细地址'=>'require',
          'phone|电话号码'=>'require',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $res=[
              'code'=>400,
              "msg"=>$validate,
            ];
            return json($res);
        }
        if(!preg_match("/^1[34578]{1}\d{9}$/",$params['phone'])){
            $res=[
              'code'=>400,
              'msg'=>"请输入正确的手机号码",
            ];
            return json($res);
        }
        // 组织要的数据 修改数据
        $params['user_id']=$user;
        $params['area']=$params['city'].'_'.$params['shi'].'_'.$params['qu'].'';
//        dump($params['area']);die;
        // 使用update方法进行更新
        $id=cache('update_id');
        $data=\app\common\model\Address::update($params,['id'=>$id],true);
//        dump($data);die;
        if(!$data){
            // 添加失败
            $res=[
              'code'=>400,
              'msg'=>"数据修改失败",
            ];
            return json($res);
        }else{
            // 修改成功
            $res=[
              'code'=>200,
              'msg'=>"修改成功",
            ];
            return $res;
        }
    }

    // 设置默认选项
    public function status(){
//        die;
        $params=request()->get();
        foreach ($params as $k=>$v){
            $id=$k;
        }
        // 根据这个data 查询数据表
        $data=\app\common\model\Address::find($id);
//        dump($data);die;
        if($data['status']==0){
//            echo 111;die;
            // 如果等于1  就把页面的值改成  那么就是要改成 0 0是不默认
            $date=\app\common\model\Address::where('id',$data['id'])->find();
            $date->status=1;
            $date->save();
            $res=[
              'code'=>200,
              'msg'=>"修成成功",
            ];
            return json($res);
        }elseif($data['status']==1){
            $date=\app\common\model\Address::where('id',$data['id'])->find();
            $date->status=0;
            $date->save();
            $res=[
                'code'=>201,
                'msg'=>"修成成功",
            ];
            return json($res);
        }else{
            // 请输入有效的参数
            $res=[
              'code'=>400,
              'msg'=>"请设置有效的参数"
            ];
            return json($res);
        }
//        dump($data);die;

    }

}
