<?php
namespace app\home\controller;

use think\Controller;

class Index extends Base
{
    public function index()
    {
        $lives = \app\common\model\Live::order('id desc')->limit(6)->select();
        //渲染模板
        return view('index', ['lives'=>$lives]);
    }


    // 渲染商家入驻页面
    public function shopinto(){
        // 渲染页面
        return view('shopinto');
    }
}