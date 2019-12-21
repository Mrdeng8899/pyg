<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Auth extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //接收参数
        $params=input();
        // 查询数据
        $list=\app\common\model\Auth::select();
        // 先转换成标准的二维数组
        $list=(new \think\Collection($list))->toArray();
        if(!isset($params['type']) && $params['type'] == 'tree'){
            // 转换成父子级树装列表结构
            $list=get_tree_list($list);
        }else{
            // 转换成无限极分类列表结构
            $list=get_cate_list($list);
        }
        // 返回数据
        $this->ok($list);
    }
    /*
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //  接收参数
        $params=input();
        $rule=[
          'auth_name|权限名称','require',
          'pid|上级权限','require|integer|egt:0',
          'is_nav|是否菜单','require|in:0,1',
        ];
        // 处理level 和pid_path
        if($params['pid']==0){
            // 添加的是顶级权限
            $params['level']=0;
            $params['pid_path']=0;
        }else{
            // 根据 pid查询上级权限
            $p_info=\app\common\model\Auth::find($params['pid']);
            if(!$p_info){
                $this->fail("数据异常");
            }
            $params['level']=$p_info['level']+1;
            $params['pid_path']=$p_info['pid_path'].'_'.$p_info['id'];
        }
        // 添加数据
        $auth=\app\common\model\Auth::create($params,true);
        $this->ok($auth);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 查询一条数据
        $auth=\app\common\model\Auth::find($id);
        $this->ok($auth);
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
        // 接收 参数
        $params=input();
        $rule=[
            'auth_name|权限名称','require',
            'pid|上级权限','require|integer|egt:0',
            'is_nav|是否菜单','require|in:0,1',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 处理level和pid_path数据
        if($params['pid']==0){
            $params['level']=0;
            $params['pid_path']=0;
        }else{
            // 根据id查询上级的权限信息
            $p_info=\app\common\model\Auth::find($params['pid']);
            if(!$p_info){
                $this->fail("数据异常");
            }
            $params['level']=$p_info['level']+1;
            $params['pid_path']=$p_info['pid_path'].'_'.$p_info['id'];

            // 不能降级 先查询原来的级别 比较要修改的级别
            $info=\app\common\model\Auth::find($id);
            if(!$info){
                $this->fail("数据异常");
            }
            // 原来级别是1 现在级别是2 降级
            if($info['level'] < $params['level']){
                $this->fail("不能降级");
            }
        }
        // 修改数据
        \app\common\model\Auth::update($params,[],true);
        // 返回数据
        $info=\app\common\model\Auth::find($id);
        $this->ok($info);
    }

    /*
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 权限有子权限不能删除
        $total=\app\common\model\Auth::where('pid',$id)->count('id');
        if($total > 0){
            $this->fail("权限有子权限不能删除");
        }
        // 删除数据
        \app\common\model\Auth::destroy($id);
        $this->ok();
    }

    // 菜单权限接口
    public function nav(){
        // 获取当前登入的管理员用户id
        $user_id=input('user_id');
        // 查询角色id
        $info=\app\common\model\Admin::find($user_id);
        $role_id=$info['role_id'];
        // 判断是否是超级管理员
        if($role_id == 1){
            // 超级管理员 直接查询权限表 直接查询权限表 菜单权限is_nav=1
            $data=\app\common\model\Auth::where('is_nav',1)->select();
        }else{
            // 其他管理员
            $role=\app\common\model\Role::find($role_id);
            $role_auth_ids=$role('role_auth_ids');
            // 转换成父子级   树状结构
            // 再查询权限表 拥有的菜单权限
            $data=\app\common\model\Auth::where('id','in',$role_auth_ids)->where('is_nav',1)->select();
        }
            $data=(new \think\Collection($data))->toArray();
            $data=get_cate_list($data);
            $this->ok($data);

    }


}
