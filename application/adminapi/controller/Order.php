<?php

namespace app\adminapi\controller;

use think\Controller;
use think\Request;

class order extends BaseApi
{
    /*
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //分页+搜索
        // 接收参数
        $where=[];
        $params=input();
        if(!empty($params['keyword'])){
                // 不为空就是查询
                $keyword = $params['keyword'];

                $where['consignee'] = ["like", "%{$keyword}%"];

        }
        if(!empty($params['start_time'])){
            // 不为空就是查询

            $where['create_time'] = ['>', strtotime($params['start_time'])];

        }
        if(!empty($params['over_time'])){
            // 不为空就是查询

            $where['create_time'] = ['>', strtotime($params['over_time'])];

        }
        $res=\app\common\model\Order::with('user')->where($where)->paginate(10);
        $this->ok($res);
    }

    /*
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /*
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {

    }

    /*
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //  查询一条信息
        $res=\app\common\model\Order::with('user')->find($id);
    }

    /*
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /*
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //  接收参数
        $params=input();
        //定义要验证的参数
        $rule=[
          'consignee|收货人名字','require',
          'address|收货地址','require',
          'phone|电话号码','require|integer',
          'shipping_name|物流名称','require',
          'shipping_sn|物流单号','require',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->fail($validate,400);
        }
        // 如果订单已经 状态已经完成 就不不能更改  待收货也不能更改
        if($params['order_status'] =2 || $params['order_status']==4){
                $this->fail("订单已经完成||订单已经发货不能修改");
        }
        if($params['order_status'] ==7){
            $this->fail("订单已经退款,不能更改信息");
        }
        if($params['order_status' ==6] || $params['order_status']== 5){
            $this->fail("订单已经退货,或者已经取消不能更改");
        }
        $res=\app\common\model\Order::update($params,[],true);
        // 查询一条信息返回
        $info=\app\common\model\Order::find($res['r_id']);
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
        //
       $status=\app\common\model\Order::where('id','=',$id)->value('order_status');
       if($status>=3){
            $this->fail("订单状态是 代付款|待发送|待收货|待评价是不能删除的",400);
       }
       //  可以删除此条订单
        \app\common\model\Order::destroy($id);
       $this->ok();
    }
}
