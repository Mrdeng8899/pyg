<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class Brand extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 接收参数 keyword page
        $params=input();
        // 查询分类下的品牌 如果传过来cate_id  就是查询分类下的品牌
        if(!empty($params['cate_id'])){
            $list=\app\common\model\Brand::field('id,name')->where('cate_id',$params['cate_id'])->select();
            $this->ok($list);
        }
        // 搜索分页
        $where=[];
        if(!empty($params['keyword'])){
            // 不为空就是搜索
            $keyword=$params['keyword'];
            $where['t1.name']=['like',"%{$keyword}%"];
        }
        // 分页查询
        $list= \app\common\model\Brand::alias('t1')
        ->join('pyg_category t2','t1.cate_id=t2.id','left')
        ->where($where)
        ->order('t1.sort desc,t1.id desc')
        ->paginate(7);
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
        // 参数验证设置
        $rule=[
            'name|品牌名称','require',
            'cate_id|所属分类','require|integer|gt:0',
            'is_hot|是热门','require|in:0,1',
            'sort|排序','require|integer',
        ];
        // 参数检测
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,404);
        }
        // 品牌logo处理
        if(!empty($params['logo']) && is_file('.'.$params['logo'])){
            \think\Image::open('.'.$params['logo'])->thumb(100,50)->save('.'.$params['logo']);
        }
        // 添加数据
        \app\common\model\Brand::create($params,true);
        $this->ok();
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        // 查询一条信息
        $info= \app\common\model\Brand::find($id);
        $this->fail($info);
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
            'name|商品品牌','require',
            'cate_id|所属分类','require|integer|gt:0',
            'is_hot|是否热门','require|in:0,1',
            'sort|商品排序','require|integer',
        ];
        // 参数检测
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 缩略图 品牌logo处理
        if(!empty($params['logo']) && is_file('.'.$params['logo'])){
            \think\Image::open('.'.$params['logo'])->thumb(100,50)->save('.'.$params['logo']);
        }
        // 修改数据
        \app\common\model\Brand::update($params,[],true);
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
        // 品牌下如果有数据 就不能删除
        $total= \app\common\model\Brand::where('id','=',$id)->count('id');
        if($total){
            $this->fail("品牌下面有数据不能进行删除");
        }
        // 删除数据
        \app\common\model\Brand::destroy($id);
        $this->ok();
    }
}
