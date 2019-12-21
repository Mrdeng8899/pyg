<?php

namespace app\adminapi\controller;

use app\adminapi\logic\AuthLogic;
use think\Controller;

class BaseApi extends Controller
{
    //无法登入检测的接口
    protected $no_login=['login/login','login/verify'];

    // 初始化方法
    public function _initialize(){
        parent::_initialize();
        header('Access-Control-Allow-Origin:*');
        //允许的请求头信息
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        //允许的请求类型
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
        //允许携带证书式访问（携带cookie）
        header('Access-Control-Allow-Credentials:true');

        // 登录检测
        $this->checkLogin();

        // 权限检测
        $res=\app\adminapi\logic\AuthLogic::check();
        if(!$res){
            $this->fail("无权访问");
        }


    }
    public function checkLogin(){
        // 获取当前访问的控制器和方法名称
        try{
            // 获取当前访问的控制器和方法名称
            // 获取控制器的名称

            $controller=request()->controller();

             // 获取 方法的名称
            $action=request()->action();
            // 使用strolower 函数 将字符串转换成小写 把控制器名字 和方法名 使用/ 合在一起
            $path=strtolower($controller.'/'.$action);

            //  判断 $path 在不在no_login这个数组里面 如果不在 就是需要登入检测
            if(!in_array($path,$this->no_login)){

                // 需要登录检测 尝试去取 token 里面的id
                $user_id=\tools\jwt\Token::getUserId();

                // 如果有空 就是有问题 报错
                if(empty($user_id)){
                    // 提示未登入 取不到id
                    $this->fail('未登入或token无效',400);
                }
                // 可以将登录的用户id 记录到当前的请求对象中去 后续需要使用用户id  直接从请求对象中获取
                request()->get(['user_id'=>$user_id]);
                request()->post(['user_id'=>$user_id]);
            }
        }catch(\Exception $e){
            $this->fail('token无效',400);
        }
    }

    // 响应
    /*
     * 通用响应
     * @param int $code 错误码
     * @param string $msg 错误描述
     * @param array $data 返回数据
     * */
    public function response($code=200,$msg='success',$data=[]){
        $res=[
          'code'=>$code,
          'msg'=>$msg,
          'data'=>$data
        ];
        echo json_encode($res);die;
    }
    // 失败的时候的响应
    /*
     * @param string $msg 错误描述
     * @param int $code 错误码
     * */
    public function fail($msg='fail',$code=500){
        return $this->response($code,$msg);
    }
    /*
     * 成功时响应
     * @param array $data 返回数据
     * @param int $code 错误码
     * @param string $msg 错误描述
     * */
    public function ok($data=[],$code=200,$msg='success'){
        return $this->response($code,$msg,$data);
    }
}
