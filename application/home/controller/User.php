<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class User extends Base{
   // 个人信息展示页面
    public function info(){
        $user=session('login_info.id');
        $info=\app\common\model\User::where('id',$user)->find();
//        dump($user);die;
        return view('userinfo',['info'=>$info]);
    }

    // 修改个人的基本信息
    public function edit(){
        $user=session('login_info.id');
        // 接收参数
        $params=input();
//        dump($params);die;
        // 组装生日日期
        if($params['sex']==1){
            $params['sex']="男";
        }else{
            $params['sex']="女";
        }

        $params['birthday']=$params['old'].$params['month'].$params['day'];
//        dump($params);die;
        // 参数检测
        $rule=[
            'nickname|昵称','require',
            'sex|性别','require|integer|in:0,1',
        ];
        $validate=$this->validate($params,$rule);
            if($validate !==true){
                $this->error($validate);
            }
        if(!preg_match('/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u',$params['nickname'])){
            $this->error("用户名由2-16位数字或字母、汉字、下划线组成！");
        }
        if(!preg_match('/^[A-Za-z0-9_\x{4e00}-\x{9fa5}]+$/u',$params['career'])){
            $this->error("请输入正确的职业");
        }
//        $users=\app\common\model\User::where('id',$user)->find();
////        dump($users);die;
//        $users->nickname = $params['nickname'];
//        $users->save();
        // 修改用户表
        \app\common\model\User::update($params,['id'=>$user],true);
        $this->success("修改成功");
    }
    public function file(){
        $user=session('login_info.id');
        $info=\app\common\model\User::where('id',$user)->find();
        // 接收参数
        $file=request()->file('user_header');
//        dump($file);die;
        if(!$file){
            $this->error("请上传您的头像");
        }
        // 上传了 设置文件的大小和 允许的类型 运行图片的格式 移动到指定的为
        $info=$file->validate(['size'=>5*1024*1024,'ext'=>'jpg,png,gif,jpeg','type'=>'image/jpeg,image/png,image/gif'])->move(ROOT_PATH.'public'.DS.'uploads');
//        dump($info);die;
        if($info){
            // 上传成功 并移动到了指定的位置  获取并且拼接访问的文件路径
            $user_header=DS.'uploads'.DS.$info->getSaveName();
//               $image=\app\common\model\User::update($file,['id'=>$user])->find();
            $image=\app\common\model\User::where('id',$user)->find();
            $image->user_header=$user_header;
            $image->save();
            $this->success("上传成功",'home/user/info');
//            return $user_header;
        }else{
            // 上传失败
            $error_msg=$file->getError();
            $this->error($error_msg);
        }
    }

    // 账号密码 修改页面
    public function safa(){
        $user=session('login_info.id');
        $safa=\app\common\model\User::where('id',$user)->find();
//        dump($safa);die;
        return view('usersafa',['safa'=>$safa]);
    }

    // 修改密码
    public function password()
    {
        $user=session('login_info.id');
        $params = input();
//        dump($params);die;
        //参数检测
        $rule = [
            'nickname|姓名' => 'require',
            'password|旧密码' => 'require',
            'confirm_password|新密码' => 'require'
        ];
        $validate = $this->validate($params, $rule);
        if ($validate !== true) {
            $this->error($validate);
        }
        // 把密码 加密  然后 查询数表
        $params['password'] = encrypt_password($params['password']);
        // 查询数据表  根据 就密码 查询数据表
        $users = \app\common\model\User::where('nickname', $params['nickname'])->where('password', $params['password'])->find();
        if (empty($users)) {
            // 没有查询到数据
            $this->error("密码错误,请重新输入");
        }
        // 查询到了  可以更改密码
        // 加密新密码
        if(!preg_match('/^[0-9a-z_$]{6,16}$/i',$params['confirm_password'])){
            $this->error('这不是一个合法的密码');
        }
        $params['confirm_password'] = encrypt_password($params['confirm_password']);
        //更新密码
//        $update=\app\common\model\User::update($params['confirm_password'],['id'=>$user],true);
        $res=\app\common\model\User::where('id',$user)->find();
        $res->password=$params['confirm_password'];
        $res->save();
        // 请求session
        session('login_info',null);
        $this->success("修改成功");
    }

    // 手机电话号码  ajax 发来请求要验证码的
    public function phone(){
        $user=session('login_info.id');
        $params=input('phone');
//        dump($params);die;
        if(empty($params)){
            $res=[
              'code'=>400,
              'msg'=>"电话号码不能为空"
            ];
            return json($res);
        }
        // 查询数据表 看看当前用户 是不是这个手机号
        $phone=\app\common\model\User::where('id',$user)->where('phone',$params)->find();
        if(!$phone){
            // 提交的电话号码和那个不符合
            $res=[
              'code'=>400,
              'msg'=>"请输入正确的原始密码",
            ];
            return json($res);
        }
        // 生成随机数
        $code=mt_rand(10000,99999);
         // 调用接口 这里调用的是邮箱的接口发送的验证码
//        $res=send_email($params,$code,'验证码');
        $res=true;
        if($res == true){
            // 发送成功 ; 记录缓存 做判断使用
            cache("code",$code,180);
            $res=[
                'code'=>200,
                'msg'=>"验证码发送成功",
                'codes'=>$code,
            ];
            echo json_encode($res);die;
        }else{
            $res=[
              'code'=>401,
              'msg'=>"验证码发送失败",
            ];
            echo json_encode($res);die;
        }
    }
    // ajax 获取新的 手机的验证馬
    public function newphone(){
//        $user=session('login_info.id');
        $params=input('phone');
//        dump($params);die;
        if(empty($params)){
            $res=[
              'code'=>400,
               'msg'=>"请输入手机号",
            ];
            return json($res);
        }
        if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',$params)){
                $res=[
                  'code'=>400,
                   'msg'=>"请输入正确的手机账号"
                ];
                return json($res);
        }
        // 生成随机数
        $code=mt_rand(10000,99999);
        // 调用接口然后数据发送出去 调用调用邮箱的
//        $res=send_email($params,$code,"验证码");
        $co=true;
        if($co ==true){
            // 验证码发送成功
            //记录缓存 验证判断的时候使用
            cache('newcode',$code,180);
            $res=[
                'code'=>200,
                'msg'=>"验证发送成功",
                'codes'=>$code,  // 实际开发不需要这样 调用接口即可
            ];
            return json($res);
        }else{
            // 验证码发送是失败
            $res=[
              'code'=>400,
              'msg'=>"验证码发送失败,请检查您的手机号码是否正确"
            ];
            return json($res);
        }
    }

    // 更改手机号码
    public function call(){
        $user=session('login_info.id');
        $params=input();
//        dump($params);
        $rule=[
          'phone'=>'require',
          'code'=>'require',
          'newphone'=>'require',
          'codes'=>'require',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->error($validate);
        }
        // 判断是不是正确的手机号
        if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',$params['phone']) || !preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',$params['newphone'])){
                     //两个手机号不是正确的
            $this->error("请输入正确的手机号");

        }
        // 取出缓存 判断他的验证码 正不正确
        $code=cache('code');
        if($code !=$params['code']){
            $this->error("请输入正确的旧的验证码");
        }
        $codes=cache('newcode');
        if($codes !=$params['codes']){
            $this->error("请输入正确的绑定的手机验证码");
        }
        // 验证通过  添加到数据表
        $data=\app\common\model\User::where('id',$user)->find();
        $data->phone=$params['newphone'];
        $data->save();
        $this->success("修改成功",'home/user/safa');
    }
}
