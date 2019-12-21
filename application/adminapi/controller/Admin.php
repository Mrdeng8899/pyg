<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Admin extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(){
        // 展示列表数据信息
        // 接收参数
        $params=input();
        $where=[];
        if(!empty($params['keyword'])){
            $keyword=$params['keyword'];
            $where['username']=['like',"%{$keyword}%"];
        }
        // 查询数据
        $list=\app\common\model\Admin::with('role_bind')->where($where)->paginate(2);
        // 返回数据
        $this->ok($list);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 接收参数
        $params=input();
        //定义验证参数
        $rule=[
          'username|用户名','require|unique:admin,username',
          'email|邮箱','require|email',
          'role_id|所属的角色','require|integer|gt:0',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 密码要加密
        if(empty($params['password'])){
            $params['password']='123456';
        }
        $params['password']=encrypt_password($params['password']);
        //  添加数据
        \app\common\model\Admin::create($params,true);
        $this->ok();
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id){
        //
        $info=\app\common\model\Admin::find($id);
        $this->ok($info);
    }


    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        // 修改 如果是超级就不能修改
        if($id==1){
            $this->fail("无权修改此管理员");
        }
        // 接收参数
        $params=input();
        // 重置密码的功能
        if(!empty($params['type']) && $params['type'] =='reset_pwd'){
                $params=['password'=>encrypt_password('123456')];
        }else{
            // 修改其他数据
            $rule=[
              'nickname|昵称' ,'max:100',
              'role_id|所属角色','integer|gt:0',
              'email|邮箱','email',
            ];
            $validate=$this->validate($params,$rule);
            if($validate !==true){
                $this->fail($validate);
            }
            if(isset($params['password'])) unset($params['password']);
            if(isset($params['username'])) unset($params['username']);
        }
        // 修改数据
        \app\common\model\Admin::update($params,[],true);
        // 返回数据
        $this->ok();
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 超级管理员  不能删除
        if($id==1){
            $this->fail("不能删除");
        }
        // 不能删除自己
        $user_id=input('user_id');
        if($user_id==$id){
            $this->fail("不能删除自己");
        }
        // 数据数据
        \app\common\model\Admin::destroy($id);
        $this->ok();
    }
}
