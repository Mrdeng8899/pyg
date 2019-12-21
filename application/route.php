<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
//定义后台接口模块的模块域名路由
Route::domain('adminapi.tpshop.com',function (){
    Route::get('','adminapi/index/index');

    // 获取验证码地址接口    路由
    Route::get('verify','adminapi/login/verify');

    \think\Route::get('captcha/[:id]', "\\think\\captcha\\CaptchaController@index");

    // 登入接口     路由
    Route::post('login','adminapi/login/login');

    // 退出接口    路由
    Route::get('logout','adminapi/login/logout');

    // 单文件上传接口 路由
    Route::post('logo','adminapi/upload/logo');

    // 多文件上传 路由
    Route::post('images','adminapi/upload/images');

    // 资源路由  商品分类
    Route::resource('categorys','adminapi/category');

    // 资源路由  商品品牌
    Route::resource('brand','adminapi/brand');

    // 定义路由 商品模型
    Route::resource('types','adminapi/type');

    // 定义商品列表的资源路由
    Route::resource('goods','adminapi/goods');

    // 删除相册图片接口
    Route::delete('delpics/:id','adminapi/goods/delpics');

    //资源路由 订单列表
    Route::resource('orders','adminapi/order');

    // 菜单权限
    Route::get('nav','adminapi/auth/nav');
//    Route::get('test','adminapi/index/test');

    // 定义角色列表 资源路由
    Route::resource('roles','adminapi/role');

    // 定义管理员接口  资源路由
    Route::resource('admins','adminapi/admin');


});