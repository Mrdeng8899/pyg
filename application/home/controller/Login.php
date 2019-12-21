<?php

namespace app\home\controller;

use think\Controller;

class Login extends Controller
{
    // 登入
    public function login(){
        $this->view->engine->layout(false);
        return view();
    }
    // 注册
    public function register(){
        $this->view->engine->layout(false);
        return view();
    }
    // 手机号注册 表单提交
    public function phone(){
        // 接收参数
        $params=input();
        dump($params);
        // 参数检测
        $rule=[
          'phone|手机号','require|regex:1[3-9]\d{9}|unique:user',
          'code|验证码','require',
          'password|密码','require|length:6,18',
          'repassword|重新确定密码','require|length:6,18|confirm:password',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->error($validate);
        }
        // 验证码效验 使用缓存标识codes
        $code=cache('codes'.$params['phone']);
        if($code !=$params['code']){
            $this->error("验证码错误");
        }
        // 验证码 验证完成后 清除缓存
        cache('codes'.$params['phone'],null);
        // 添加用户
//        dump($params);die;
        $params['username']=$params['phone'];
        $params['nickname']=encrypted_phone($params['phone']);

        // 密码加密
        $params['password']=encrypt_password($params['password']);
        \app\common\model\User::create($params,true);
        // 页面跳转
        // 跳转到登入页面去
        $this->success('注册成功','login');
    }
    // 发送验证码
    public function sendcode(){
        // 接收参数
        $phone=input('phone');
        // 参数检测
        if(!preg_match('/^1[3-9]\d{9}$/',$phone)){
            $res=[
                'code'=>400,
                'msg'=>'手机号码格式不正确',
                ''
            ];
            return json($res);
        }
        // 检测是否频繁发送请求
        $list_time=cache('times'.$phone ?:0);
        if(time() - $list_time <60){
            $res=[
              'code'=>400,
               'msg'=>'操作太频繁了请重新再试一下',
            ];
            return json($res);
        }

        //  生成随机数
        $code=rand(1000,9999);
        // 验证码模板
        $msg= '【创信】你的验证码是：' . $code . '，3分钟内有效！';
        $result=send_msg($phone,$msg);
//        $result=true;
//        echo $result;die;
        if($result ===true){
            // 发送成功
            // 将验证码 记录到缓存中用于后续验证
            cache('codes'.$phone,$code,180);
            cache('times'.$phone,time(),180);
            $res=[
              'code'=>200,
              'msg'=>'发送成功',
            ];
            return json($res);
        }else{
            // 发送失败
            $res=[
              'code'=>401,
              'msg'=>'发送失败',
            ];
            return json($res);
        }
    }
    // 登入页面
    public function dologin(){
        # 接收参数
        $params=input();
        # 参数检测
        $rule=[
          'username|用户名','require',
           'password|密码','require',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->error($validate,400);
        }
        // 拿密码 进去加密 做判断是否正确
        $password=encrypt_password($params['password']);
//        echo $password; die;
        // 查询user表
        $user=\app\common\model\User::where(function($query)use($params){
            $query->where('phone',$params['username'])->whereOr('email',$params['username']);})->where('password',$password)->find();
        if($user){
            //设置 session 设置登入标识
            session('login_info',$user->toArray());
            // 迁移cookie购物车数据到数据表
            \app\home\logic\CartLogic::cookieToDb();
            // 关联第三方用户
            if(session('open_type') && session('open_id')){
                $open_user=\app\common\model\OpenUser::where('open_type',session('open_type'))->where('openid',session('open_id'))->find();
                $open_user->user_id=$user['id'];
                $open_user->save();
            }
            if(session('open_nickname')){
                \app\common\model\User::update(['nickname'=>session('open_nickname')],['id'=>$user['id']],true);
            }
            // 页面跳转 从session 获取跳转地址
            $back_url=session('back_url') ?:'home/index/index';
            // 登入 跳转到首页
            $this->redirect($back_url);
        }else{
            $this->error("账号或者密码错误");
        }
    }
    // 退出页面
    public function logout(){
        // 删除session
            session(null);
            $this->redirect('home/login/login');
    }
    // qq 登入回调函数
    public function qqcallback(){
//        echo 'hello';
        // 参考 plugins/qq/example/oauth/callback.php
        require_once("./plugins/qq/API/qqConnectAPI.php");
        $qc=new \QC();
        // 得到的是一个token
        $access_token=$qc->qq_callback();
        // 用户id
        $open_id=$qc->get_openid();

        // 获取用户信息 昵称
        $qc = new \QC($access_token,$open_id);
        $info=$qc->get_user_info();
//        dump($info['nickname']);die;

        // 关联用户
        $open_user=\app\common\model\OpenUser::where('open_type','qq')->where('openid',$open_id)->find();
        if($open_user &&empty($open_user['user_id'])){
            // 已经关联过的  同步信息昵称  查询数据
            $user=\app\common\model\User::find($open_user['user_id']);
            // 获取登入者的昵称
            $user->nickname=$info['nickname'];
            // 保存
            $user->save();
            // 登入成功 记录session
            session('login_info',$user->toArray());
            // 迁移cookie购物车数据到数据表
            \app\home\logic\CartLogic::cookieToDb();
            // 页面跳转 从session 获取跳转地址
            $back_url=session('back_url') ?:'home/index/index';
            // 登入 跳转到首页
            $this->redirect($back_url);
        }else{
            //  以前有没登入过这个网站的 给用户选择一个页面
            // 记录添加到 open_user表
            if(!$open_user){
                \app\common\model\OpenUser::create([
                   'open_type'=>'qq',
                   'openid'=>$open_id,
                ]);
            }
            // 第三方 账号 信息放到 session 用于后续登入后关联用户
            session('open_type','qq');
            session('open_id',$open_id);
            session('open_nickname',$info['nickname']);

            // 这里跳转到登入页 真正的业务逻辑 是跳转到其他页面 让用户是否关联
            $this->redirect('home/login/login');
        }
    }
    // 支付宝 登入 接口 回调函数
    public function alicallback(){
//        echo 111;
        // 引入必要的文件
        require_once './plugins/alipay/oauth/config.php';
        require_once './plugins/alipay/oauth/service/AlipayOauthService.php';
        // 实例化AlipayOauthService
        $obj=new\AlipayOauthService($config);
        // 获取 auth_code
        $auth_code=$obj->auth_code();
        // 获取 access_toke
        $access_toke=$obj->get_token($auth_code);
        // 获取用户信息
        $info=$obj->get_user_info($access_toke);
        $openid=$info['user_id'];
//        dump($info);die;
        if(!isset($info['nick_name'])){
//            $info['nick_name'] = '';
            $info['nick_name'] = $openid;
        }
        // 接下来 就是 关联绑定用户的过程
        // 判断是否关联
        $open_user=\app\common\model\OpenUser::where('open_type','alipay')->where('openid',$openid)->find();
//        dump($open_user);
        if($open_user && $open_user['user_id']){
            // 已经关联了 的直接登入 记录session
            // 同步 用户信息 到用户表
            $user=\app\common\model\User::find($open_user['user_id']);
//            dump($user);
            $user->nickname=$info['nick_name'];
            $user->save();
            // 设置登入标识
            session('login_info',$user->toArray());
            // 迁移cookie购物车数据到数据表
            \app\home\logic\CartLogic::cookieToDb();
            // 页面跳转 从session 获取跳转地址
            $back_url=session('back_url') ?:'home/index/index';
            // 登入 跳转到首页
            $this->redirect($back_url);
        }
        if(!$open_user){
            // 第一次登入 没哟记录 添加一条记录到openuser表
            $open_user=\app\common\model\OpenUser::create(['open_type'=>'alipay','openid'=>$openid]);
        }
        // 让第三方 账号去关联用户 可能是注册 可能是登入
        session('open_user_id',$open_user['id']);
        session('open_nickname',$info['nick_name']);
        $this->redirect('home/login/login');
    }
    // 邮箱注册页面
    public function emailzc(){
        $this->view->engine->layout(false);
        return view('email');
    }
    // 邮箱注册
    public function email(){
        if(request()->isGet()){
            $this->error("非法请求");
        }
        $params=input();
        $rule=[
          'email','require|email',
          'code','require|integer',
          'password','require|integer',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->error($validate);
        }
        $code=cache('email'.$params['email']);
        if($code !=$params['code']){
            $this->error('验证码错误');
        }
        // 验证码用户以后
        cache('email'.$params['email'],null);
        // 添加用户
        $params['username']=$params['email'];
        $params['nickname']=$params['email'];
        $params['password']=encrypt_password($params['password']);
        \app\common\model\User::create($params,true);
        $this->success('注册成功','home/index/index');
    }
    // 发送邮箱的验证码
    public function emails(){
            // 发送验证码
        // 接收参数
        $email=input('email');
        if(!preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/",$email)){
            $res=[
                'code'=>400,
                'msg'=>'邮箱格式不正确',
            ];
            return json_encode($res);
        }
        // 判断邮箱是不是邮件有这个用户了 先查询出来
        $user=\app\common\model\User::where('email',$email)->find();
//        if($user){
//            $res=[
//                'code'=>400,
//                'msg'=>'此邮箱已经给注册了',
//            ];
//            return $res;die;
//        }
        $code=mt_rand(10000,99999);
        $msg='你用于品优购邮箱注册的验证码是'.$code.'三分钟是有效的,请及时验证!';
        $subject="品优购注册";
        $recipients=$email;
        $result=send_email($recipients,$msg,$subject);
        if($recipients == true){
            // 邮件发送成功
            cache('email'.$email,$code,180);
            $res=[
              'code'=>200,
              'msg'=>'邮件发送成功',
            ];
            return json($res);
        }else{
            $res=[
              'code'=>400,
              'msg'=>'邮件发送失败',
            ];
            return json($res);
        }
    }
}
