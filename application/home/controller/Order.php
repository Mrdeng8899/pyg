<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Order extends Base
{
    // 登入检测
    public function create(){
        if(!session('login_info')){
            //
            session('back_url','home/cart/index');
            $this->redirect('home/login/login');
        }
        // 获取用户收货信息
        $user_id=session('login_info.id');
//        dump($user_id);die;
        $address=\app\common\model\Address::where('user_id',$user_id)->select();
//        dump($address);die();
        // 查询 商品清单信息 选中的购物记录以及 商品信息
        $res=\app\home\logic\OrderLogic::getCartWithGoods();
        $res['address']=$address;

        return view('create',$res);
    }

    public function save(){
        // 接收参数
        $params=input();
//        dump($params);die;
        // 参数检测
        $rule=[
            'address_id','require|integer|gt:0',
        ];
//        dump($rule);die;
        $validate=$this->validate($params,$rule);
//        dump($validate);die;
        if($validate !==true){
//        echo 11;die;
            $this->error($validate);
        }
        // 查询收货地址信息 把传过来的 id 作为查询 条件
        $user_id=session('login_info.id');
        $address=\app\common\model\Address::find($params['address_id']);
//        dump($address);die;
        // 可能不存在 判断
        if(!$address){
            $this->error("收货地址异常");
        }
        // 开启事务
        \think\Db::startTrans();
        try{
            // 生成订单编号
            $order_sn=time().mt_rand(10000,99999);
            // 查询选中的购物记录 计算商品的总价格
            $res=\app\home\logic\OrderLogic::getCartWithGoods();
//            dump($res);die;
            // 组装一个订单数据
            $row=[
              'user_id'=>$user_id,
              'order_sn'=>$order_sn,
              'order_status'=>0,
              'consignee'=>$address['consignee'],
              'phone'=>$address['phone'],
              'address'=>$address['area'].$address['address'],
              'goods_price'=>$res['total_price'],   // 商品的总价格
              'shipping_price'=>0,  // 邮费
              'coupon_price'=>0,  //优惠券折扣
              'order_amount'=>$res['total_price'],     // 商品的总价加上邮费-优惠券
              'total_amount'=>$res['total_price'],  //商品的总价 加上邮费
            ];
            // 添加一条数据
            $order=\app\common\model\Order::create($row,true);
            // 向订单商品 表添加多条记录
            $order_goods_data=[];
//            dump($res['data']);die;
            foreach($res['data'] as $v){
                  $order_goods_data[]=[
                    'order_id'=>$order['id'],
                    'goods_id'=>$v['goods_id'],
                    'spec_goods_id'=>$v['spec_goods_id'],
                    'number'=>$v['number'],
                    'goods_name'=>$v['goods']['goods_name'],
                    'goods_logo'=>$v['goods']['goods_logo'],
                    'goods_price'=>$v['goods']['goods_price'],
                    'spec_value_names'=>$v['spec_goods']['value_names'],
                  ];
            }

            $order_goods= new \app\common\model\OrderGoods();

            $order_goods->saveAll($order_goods_data);
            // 从购物车表 删除对应记录

            \app\common\model\Cart::destroy(['user_id'=>$user_id,'is_selected'=>1]);

            // 冻结库存
            $goods_data=[];
            $spec_goods_data=[];
            foreach($res['data'] as $v){
                if($v['spec_goods_id']){
                    // 冻结 spec_goods表的库存
                    $spec_goods_data[]=[
                      'id'=>$v['spec_goods_id'],
                        // 库存数量 商品表的数量 减去 购物车的 购买数量就是你的库存数量
                      'store_count'=>$v['goods']['goods_number'] - $v['number'],
                        // 冻结库存  商品表的 冻结数量 加上你购买的数量就是您的冻结库存数量
                       'store_frozen'=>$v['goods']['frozen_number'] +$v['number'],
                    ];
                }else{
                    // 冻结 goods 表的库存
                    $goods_data=[
                      'id'=>$v['goods_id'],
                      'goods_number'=>$v['goods']['goods_number'] -$v['number'],
                       'frozen_number'=>$v['goods']['frozen_number'] +$v['number'],
                    ];
                }
            }
            // 批量 修改库存
            $goods_model=new \app\common\model\Goods();
            $goods_model->saveAll($goods_data);
            $spec_goods_model=new \app\common\model\SpecGoods();
            $spec_goods_model->saveAll($spec_goods_data);
            // 提交事务
            \think\Db::commit();
        }catch(\Exception $e){
            // 事务回滚
            \think\Db::rollback();
            $msg=$e->getMessage();
            $line=$e->getLine();
            $this->error($msg,$line);
        }
        // 跳转到 选择支付方式的页面
        $this->redirect('pay',['id'=>$order['id']]);
    }
    // 悬着 支付方式
    public function pay($id){
        // 查询订单信息
        $order=\app\common\model\Order::find($id);
        // 支付方式
        $pay_type=config('pay_type');

        // 聚合支付
        //二维码图片中的支付链接（本地项目自定义链接，传递订单id参数）
        //$url = url('/home/order/qrpay', ['id'=>$order->order_sn], true, true);
        //用于测试的线上项目域名 http://pyg.tbyue.com
        $url = url('/home/order/qrpay', ['id'=>$order->order_sn, 'debug'=>'true'], true, "http://pyg.tbyue.com");
        //生成支付二维码
        $qrCode = new \Endroid\QrCode\QrCode($url);
        //二维码图片保存路径（请先将对应目录结构创建出来，需要具有写权限）
        $qr_path = '/uploads/qrcode/'.uniqid(mt_rand(100000,999999), true).'.png';
        //将二维码图片信息保存到文件中
        $qrCode->writeFile('.' . $qr_path);
        $this->assign('qr_path', $qr_path);




//        die($order);
        return view('pay',['order'=>$order,'pay_type'=>$pay_type]);
    }
    public function topay(){
        // 接收参数
        $params=input();
        // 参数检测
        $rule=[
          'id'=>'require|integer|gt:0',
          'pay_type|支付方式'=>'require',
        ];
        $validate=$this->validate($params,$rule);
        if($validate !==true){
            $this->error($validate);
        }
        // 查询订单信息
        $user_id=session('login_info.id');
        $order=\app\common\model\Order::where('id',$params['id'])
            ->where('user_id',$user_id)
            ->where('order_status',0)
            ->find();
        if(!$order){
            $this->error("订单数据异常");
        }
        // 记录支付的方式
        $pay_type=config('pay_type');
        $pay_name=$pay_type[$params['pay_type']]['pay_name'];
        // 修改到订单的订单表
        $order->pay_code=$params['pay_type'];
        $order->pay_name=$pay_name;
        $order->save();
        // 判断支付
        switch($params['pay_type']){
            case 'wechat':
                // 微信支付
              echo '微信支付功能测试中.暂不支持';
              break;
            case 'unionpay':
              echo '银联支付功能测试中,暂不支持';
              break;
            default:
                // 支付宝
                // order_amount 付款的金额
                $html="<form id='alipayment' action='/plugins/alipay/pagepay/pagepay.php' method='post' style='display: none'>
    <input id='WIDout_trade_no' name='WIDout_trade_no' value='{$order['order_sn']}'/>
    <input id='WIDsubject' name='WIDsubject' value='ghggg'/>
    <input id='WIDtotal_amount' name='WIDtotal_amount' value='{$order['order_amount']}' /> 
    <input id='WIDbody' name='WIDbody' value='222'/>
</form><script>document.getElementById('alipayment').submit();</script>";
                echo $html;
                break;
        }
    }
    // 同步通知 页面的跳转
    public function callback(){
        // 接收参数
        $params=input();
        // 验证签名
        require_once("./plugins/alipay/config.php");
        require_once './plugins/alipay/pagepay/service/AlipayTradeService.php';

        $alipaySevice = new AlipayTradeService($config);
        $result = $alipaySevice->check($params);
        if($result){
            // 如果需要可以查询订单的信息
//            $order=\app\common\model\Order::where('order_sn',$params['out_trade+no'])->find();
//            return view('paysuccess', ['order'=>$order]);
        // 展示页面
        return view('paysuccess',['pay_name'=>'支付宝','total_amount'=>$params['total_amount']]);
        }else{
            // 验证失败
            $msg="支付验证失败";
            return view('payfail',['msg'=>$msg]);
        }
    }
    // 异步处理  异步就是多个任务过来 同时处理 支付宝的异步处理
    public function notify(){
        // 参考 plugins/alipay/notify_url.php
        // 接收参数
        $params=input();
        // 验证签名
        require_once './plugins/alipay/config.php';
        require_once './plugins/alipay/pagepay/service/AlipayTradeService.php';
        $alipaySevice = new AlipayTradeService($config);
//        $alipaySevice->writeLog(var_export($_POST,true));  // 记录日志
        //记录日志
        trace('/home/order/notify:接收参数'.json_encode($params,JSON_UNESCAPED_UNICODE),true);
        $result = $alipaySevice->check($params);
        if(!$result){
            // 验签失败
            trace('/home/order/notify:验签失败:'.$result.'error');
            echo 'fail';die;
        }
        // 验签成功
        $trade_status=$params['trade-status'];
        if($trade_status =='TRADE_FINISHED'){
            // 超过可退款期限 出发此通告 交易已经完成 //记录日志
            trace('/home/order/notify:交易已完成:'.$trade_status,'debug');
            echo 'success';die;
        }
        if($trade_status =='TRADE_SUCCESS'){
            // 查询并检测订单
            $order_sn=$params['out_trade_no'];
            $order=\app\common\model\Order::where('order_sn',$order_sn)->find();
            if(!$order){
                trace('/home/order/notify:订单不存在'.$order_sn,'error');
                echo 'fail';die;
            }
            // 检测订单的支付金额
            if($order['order_amount'] !=$params['total_amount']){
                trace('/home/order/notify:订单金额不正确:应付金额'.$order['order_amount'].'实付款金额'.$params['total_amount']);
                echo 'fail';die;
            }
            // 检测订单的状态
            if($order['order_status'] !=0){
                trace("/home/order/notify: 订单状态不是待付款:".$order['order_status'],'debug');
                echo 'success';die;
            }
            // 修改订单的信息
            $order->order_status=1; // 已经付款 待发货
            $order->pay_code='alipay';
            $order->pay_name="支付宝";
            $order->save();
            // 记录支付信息
            \app\common\model\PayLog::create([
                'order_sn'=>$order_sn,
                'json'=>json_encode($params,JSON_UNESCAPED_UNICODE)
            ],true);
            // 扣减库存
            // 查询订单下的商品信息
            $order_goods=\app\common\model\OrderGoods::with('goods,spec_goods')->where('order',$order['id'])->select();
            $goods_data=[];
            $spec_goods_data=[];
            foreach($order_goods as $v){
                if($v['spec_goods_id']){
                    // 修改sku表
                    $spec_goods_data[]=[
                      'id'=>$v['spec_goods_id'],
                      'store_frozen'=>$v['spec_goods']['store_frozen'] - $v['number'],
                    ];
                }else{
                    // 修改商品表 spu表
                    $goods_data[]=[
                      'id'=>$v['goods_id'],
                      'frozen_number'=>$v['goods']['frozen_number'] -$v['number']
                    ];
                }
            }
            // 批量修改
            $goods_model=new \app\common\model\Goods();
            $goods_model->saveAll($goods_data);
            $spec_goods_model= new \app\common\model\SpecGoods();
            $spec_goods_model->saveAll($spec_goods_data);

            trace('home/order/notify:订单已修改','debug');
            echo 'success';die;
        }
        trace('home/order/notify: 其他交易付款状态','debug');
        echo 'success';die;
    }
    //扫码支付
    public function qrpay()
    {
        $agent = request()->server('HTTP_USER_AGENT');
        //判断扫码支付方式
        if ( strpos($agent, 'MicroMessenger') !== false ) {
            //微信扫码
            $pay_code = 'wx_pub_qr';
        }else if (strpos($agent, 'AlipayClient') !== false) {
            //支付宝扫码
            $pay_code = 'alipay_qr';
        }else{
            //默认为支付宝扫码支付
            $pay_code = 'alipay_qr';
        }
        //接收订单id参数
        $order_sn = input('id');
        //创建支付请求
        $this->pingpp($order_sn,$pay_code);
    }

    //发起ping++支付请求
    public function pingpp($order_sn,$pay_code)
    {
        //查询订单信息
        $order = \app\common\model\Order::where('order_sn', $order_sn)->find();
        //ping++聚合支付
        \Pingpp\Pingpp::setApiKey(config('pingpp.api_key'));// 设置 API Key
        \Pingpp\Pingpp::setPrivateKeyPath(config('pingpp.private_key_path'));// 设置私钥
        \Pingpp\Pingpp::setAppId(config('pingpp.app_id'));
        $params = [
            'order_no'  => $order['order_sn'],  // 订单的编号
            'app'       => ['id' => config('pingpp.app_id')],
            'channel'   => $pay_code,
            'amount'    => $order['order_amount']*100,  // 货币的单位 *100 就是已元的
            'client_ip' => '127.0.0.1',
            'currency'  => 'cny',  // 元的单位
            'subject'   => 'Your Subject',//自定义标题
            'body'      => 'Your Body',//自定义内容
            'extra'     => [],
        ];
        if($pay_code == 'wx_pub_qr'){
            $params['extra']['product_id'] = $order['id'];
        }
        //创建Charge对象
        $ch = \Pingpp\Charge::create($params);
        //跳转到对应第三方支付链接
        $this->redirect($ch->credential->$pay_code);die;
    }
    //查询订单状态
    public function status()
    {
        //接收订单编号
        $order_sn = input('order_sn');
        //查询订单状态
        /*$order_status = \app\common\model\Order::where('order_sn', $order_sn)->value('order_status');
        return json(['code' => 200, 'msg' => 'success', 'data'=>$order_status]);*/
        //通过线上测试
        $res = curl_request("http://pyg.tbyue.com/home/order/status/order_sn/{$order_sn}");
        echo $res;die;
    }

    public function payresult()
    {
        $order_sn = input('order_sn');
        $order = \app\common\model\Order::where('order_sn', $order_sn)->find();
        if(empty($order)){
            return view('payfail', ['msg' => '订单编号错误']);
        }else{
            return view('paysuccess', ['pay_name' => $order->pay_name, 'total_amount'=>$order['total_amount']]);
        }
    }

    // 渲染个人订单页面
    public function myOrder(){
        // 根据用户的user_id  查询订单表
        $user=session('login_info.id');
        $res=\app\common\model\Order::with('OrderGoods')->where('user_id',$user)->paginate(10);
//        dump($res);die;
        return view('seckillOrder',['res'=>$res]);
    }


    //  待付款 订单详情页面
    public function details($id){
//        dump($id);die;
        // 上面方法的id  是订单表里面的id
        $user=session('login_info.id');
        // 查询数据 查询 待付款的订单
        $res=\app\common\model\Order::with('OrderGoods')->find($id);
        $res=$res->toArray();
//        dump($res);die;
        return view('details',['res'=>$res]);
    }




}
