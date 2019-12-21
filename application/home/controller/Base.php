<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Base extends Controller
{
    //
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        // 查询所有的分类
        $this->getCategory();
    }
    public function getCategory(){
        // 查询所有分类的数据
        $category=\app\common\model\Category::select();
        $category=(new \think\Collection($category))->toArray();
        // 转化为父子树状 结构
        $category=get_tree_list($category);
        $this->assign('category',$category);
    }
}
