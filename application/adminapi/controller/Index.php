<?php
namespace app\adminapi\controller;

class Index extends BaseApi{
    public function index()
    {
//        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
//        $goods= \think\Db::table('pyg_goods')->find();
//        dump($goods);die;

//        $this->fail(['action'=>'index']);
//        $password=$this->encrypt_password('123456');
//        echo $password;die;
//        $password=encrypt_password('123456');
//        echo $password;
//         //一对一关联查询
//        $info= \app\common\model\Admin::find(1);
////        dump($info);die;
//        $this->ok($info->profile);

        // 关联 预载入  使用 with方法
//        $info= \app\common\model\Admin::with('profileBind')->find(1);
////        $this->ok($info);     # 形成的是一个 有一个 二级数据 profile 里面的所有数据
//        $this->ok($info);     #

        // 一对多查询
        // 查询 brands下面的所有表的信息
//        $info=\app\common\model\Category::with('brands')->find(72);
//        $this->ok($info);

        //   一对一
//        $info=\app\common\model\Brand::with('category')->find(1);
//        $info=\app\common\model\Brand::with('categoryBind')->find(1);
//        $this->ok($info);

    }


}
