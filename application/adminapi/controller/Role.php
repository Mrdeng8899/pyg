<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Role extends BaseApi
{
    /*
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 查询所有的角色
        $list=\app\common\model\Role::select();
        // 对每个角色 需要查询拥有去哪选
        foreach ($list as $k=>$v){
            if($v['id'] == 1){
                // 那么它是超级管理员
                $where=[];
            }else{
                // 普通管理员
                $where['id']=['in',$v['role_auth_ids']];
            }
            $role_auths=\app\common\model\Auth::where($where)->select();
            // 转换为父子树状结构
            $role_auths=(new \think\Collection($role_auths))->toArray();
            $role_auths=get_tree_list($role_auths);
            $list[$k]['role_auths']=$role_auths;
        }
        // 返回数据
        $this->ok();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
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
        // 参数验证
        $rule=[
          'role_name|角色名称'=>'require',
          'auth_ids|拥有的权限'=>'require',
        ];
        // 参数检测
        $validate=$this->validate($params,$rule);
        // 判断是不是有传来数据
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 添加数据
        if(is_array($params['auth_ids'])){
            $params['auth_ids']=implode(',',$params['auth_ids']);
        }
        $params['role_auth_ids']=$params['auth_ids'];
        $res=\app\common\model\Role::create($params,true);
        $info=\app\common\model\Role::find($res['id']);
        $this->ok($info);

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
        $info=\app\common\model\Role::find($id);
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
        // 数据的编辑
        // 接收参数
        $params=input();
        // 定义验证参数验证
        $rule=[
            'role_name|角色名称','require',
            'auth_ids|拥有的权限','require'
        ];
        // 参数检测
        $validate=$this->validate($params,$rule);
        // 判断 数据是否用
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 添加数据
        if(is_array($params['auth_ids'])){
            $params['auth_ids']=implode(',',$params['auth_ids']);
        }
        // 数据赋值
        $params['rule_auth_ids']=$params['auth_ids'];
        // 修改数据
        \app\common\model\Role::update($params,[],true);
        // 查询数据
        $info=\app\common\model\Role::find($id);
        // 返回数据
        $this->ok($info);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 角色下面如果有 管理员 不能删除
        $total=\app\common\model\Admin::where('role_id',$id)->count('id');
        if($total){
            $this->fail("角色下有管理员,不能删除");
        }
        // 删除数据
        \app\common\model\Role::destroy($id);
        // 返回
        $this->ok();
    }
}
