<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Live extends Base
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        if(!session('?login_info')){
            $this->redirect('home/login/login');
        }
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    // 个人中心展示页面
    public function index()
    {
        $user_id = session('login_info.id');
        $list = \app\common\model\Live::where('user_id', $user_id)->order('id desc')->select();
        $info=\app\common\model\User::where('id',$user_id)->find();
        return view('index', ['list'=>$list,'user_id'=>$user_id,'info'=>$info]);
    }

    // 直播页面展示
    public function create()
    {
        $user_id = session('login_info.id');
        $info=\app\common\model\User::where('id',$user_id)->find();
        return view('create',['info'=>$info]);
    }
}
