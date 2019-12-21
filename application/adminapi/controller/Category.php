<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Category extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 接收参数 pid可选择 type可选
        $params=input();
//            dump($params['pid']);die;
        if(empty($params['pid'])){
            // 查询所有的数据
            $list=\app\common\model\Category::field('id,cate_name,pid,pid_path_name,level,is_show,is_hot,is_hot,image_url')->select();
        }else{
            // 参数数据 普通列表  普通列表指的是 如果 传过来的是父级 那么显示的就是他的儿子级
            $list= \app\common\model\Category::field('id,cate_name,pid')->where('pid',$params['pid'])->select();
        }
        // 判断 $params 里面有没有type  或者$params里面的type不是list 那么就 调用toArray函数 进行转换成标准化的数据
        if(empty($params['type']) || $params['type'] !='list'){
//                        dump($params['pid']);die;
            // 将查询的结果 转化成标准的二维数组
            $list=(new \think\Collection($list))->toArray();
            // 转换为无限极分类列表
            // 调用封装好的函数 弄成 无限极的分类列表
            $list=get_cate_list($list);
        }
        // 成功 就返回这个 无限极分类的的数据
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
        # 参数检测
        $rule=[
          'cate_name|分类名称','require',
          'pid|上级分类','require|integer|egt:0',
          'is_show|是否显示','require|in:0,1',
          'is_hot|是否热门','require|in:0,1',
          'sort|排序','require|integer',
        ];
        // 参数验证
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 添加数据
        if($params['pid']==0){
            $params['pid_path_name']='';
            $params['pid_path']=0;
            $params['level']=0;
        }else{
            $p_info=\app\common\model\Category::find($params['pid']);
            $params['pid_path_name']=$p_info['pid_path'].'_'.$p_info['id'];
            $params['pid_path_name']=trim($p_info['pid_path_name'].'_'.$p_info['cate_name'],'_');
            // 就是如果您添加的商品是二级的分类 那么您此时添加的东西是一个二级下面的东西 所以level 就要加1 证明他是一个三级下面的东西
            $params['level']=$p_info['level']+1;
        }
        // 分类logo图片处理
        // 判断 上传的logo图片是不是一个logo图片 并且它是一个文件
        if(!empty($params['logo']) && is_file('.'.$params['logo'])){
            // 生成缩略图
            // 定义新的名字
            # dirname本函数返回去掉文件名后的目录名。
            $logo=dirname($params['logo']).DS.'thumb_'.basename($params['logo']);
            // 生成缩略图 open 打开你的图片 thumb 制作缩略图的大小  save 图片新名字
            \think\Image::open('.'.$params['logo'])->thumb(50,30)->save('.'.$logo);
            $params['image_url']=$logo;
        }
        // 添加数据  true 指的是过滤非表字段
        $res=\app\common\model\Category::create($params,true);
        // 返回数据     根据restful风格 返回数据 返回id
        $info= \app\common\model\Category::find($res['id']);
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
        $info= \app\common\model\Category::field('id,cate_name,pid,pid_path_name,level,is_show,is_hot,image_url')->find($id);
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
        // 接收参数
        $params=input();
        // 参数检测
        $rule=[
            'cate_name|分类名称','require',
            'pid|上级分类','require|integer|egt:0',
            'is_show|是否显示','require|in:0,1',
            'is_hot|是否热门','require|in:0,1',
            'sort|排序','require|integer',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 添加数据
        if($params['pid'==0]){
            $params['pid_path']=0;
            $params['pid_path_name']='';
            $params['level']=0;
        }else{
            $p_info=\app\common\model\Category::find($params['pid']);
            $params['pid_path']=$p_info.'_'.$p_info['id'];
            $params['pid_path_name']=trim($p_info['pid_path_name'].'_'.$p_info['cate_name'],'_');
            $params['level']=$p_info['level']+1;
        }
        // 不能降级  不能降级指的 就是假如你定义的是一个二级分类 你就不能更改成三级分类
        $info= \app\common\model\Category::find($id);
        if($info['level']<$params['level']){
            $this->fail("不能降级选择");
        }
        // 使用update方法 进行更新  [] 更新条件 true过滤非表字段
        \app\common\model\Category::update($params,[],true);
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
        // 如果分类下面有子分类不能删除
        $info=\app\common\model\Category::where('pid','=',$id)->find();
        if($info){
            $this->fail("分类下有子类不能删除");
        }
        \app\common\model\Category::destroy($id);
        $this->ok();
    }
}
