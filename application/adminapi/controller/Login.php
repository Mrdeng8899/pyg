<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Login extends BaseApi {


   // 验证码 图片接口
    public function verify(){
//        echo $this->encrypt_password('123456');die;

        //uniqid 函数设置生成一个唯一id
        $uniqid=uniqid('login',true);

        $data=[
            'url'=>captcha_src($uniqid),    // 验证码地址
            'uniqid'=>$uniqid,              //验证码标识
        ];
        // 返回数据
        $this->ok($data);
    }




    // 登入接口
    public function login(){
//        echo 11;die;
        // 接收参数
        $params=input();
        // 参数检测
        $rule=[
            'username|用户名'=>'require',
            'password|密码'=>'require',
            'uniqid|验证码编号'=>'require',
            'code|验证码'=>'captcha:'.$params['uniqid'],
        ];
        $validate=$this->validate($params,$rule);

        if($validate !==true){
            $this->fail($validate,400);
        }
        //处理数据
//        if(!captcha_check($params['code'],$params['uniqid'])){
//            $this->fail('验证码错误',400);
//        }
        // 根据用户名密码查询管理员表
        $password=encrypt_password($params['password']);
        $info= \app\common\model\Admin::where('username',$params['username'])->where('password',$password)->find();
        if($info){
            // 登录成功 生成token令牌
            $token= \tools\jwt\Token::getToken($info->id);
            $data=[
                'token'=>$token,
                'username_id'=>$info->id,
                'username'=>$info->username,
                'nickname'=>$info->nickname,
                'email'=>$info->email,
            ];
            // 返回数据
            $this->ok($data);
        }else{
            $this->fail('账号或者密码错误',401);
        }
    }

    // 退出接口
    public function logout(){
        // 将token 令牌 保存起来 作为退出过的token
        $token= \tools\jwt\Token::getRequestToken();
        # 将要退出的token  存储到缓存中
        $delete_token=cache('delete_token') ?:[];
        $delete_token[]=$token;
        // 缓存
        cache('delete_token',$delete_token,84600);
        // 返回数据
        $this->ok();
    }
}
