<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:68:"C:\newww\tpshop\public/../application/home\view\address\address.html";i:1576400084;s:49:"C:\newww\tpshop\application\home\view\layout.html";i:1576306169;}*/ ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <link rel="stylesheet" type="text/css" href="/static/home/css/all.css" />

    <script type="text/javascript" src="/static/home/js/all.js"></script>
</head>

<body>
<!-- 头部栏位 -->
<!--页面顶部-->
<div id="nav-bottom">
    <!--顶部-->
    <div class="nav-top">
        <div class="top">
            <div class="py-container">
                <div class="shortcut">
                    <ul class="fl">
                        <li class="f-item">品优购欢迎您！</li>
                        <?php if((\think\Session::get('login_info') ==null)): ?>
                        <li class="f-item">请<a href="<?php echo url('home/login/login'); ?>" target="_blank">登录</a>　<span><a href="<?php echo url('home/login/register'); ?>" target="_blank">免费注册</a></span></li>

                        <?php else: ?>
                        <li class="f-item">Hi<a href="<?php echo url('home/live/index'); ?>"><?php echo \think\Session::get('login_info.nickname'); ?></a>　<span><a href="<?php echo url('home/login/logout'); ?>" target="_blank">退出</a></span></li>
                        <?php endif; ?>
                    </ul>
                    <ul class="fr">
                        <li class="f-item">我的订单</li>
                        <li class="f-item space"></li>
                        <li class="f-item"><a href="home.html" target="_blank">我的品优购</a></li>
                        <li class="f-item space"></li>
                        <li class="f-item">品优购会员</li>
                        <li class="f-item space"></li>
                        <li class="f-item">企业采购</li>
                        <li class="f-item space"></li>
                        <li class="f-item">关注品优购</li>
                        <li class="f-item space"></li>
                        <li class="f-item" id="service">
                            <span>客户服务</span>
                            <ul class="service">
                                <li><a href="cooperation.html" target="_blank">合作招商</a></li>
                                <li><a href="shoplogin.html" target="_blank">商家后台</a></li>
                            </ul>
                        </li>
                        <li class="f-item space"></li>
                        <li class="f-item">网站导航</li>
                    </ul>
                </div>
            </div>
        </div>

        <!--头部-->
        <div class="header">
            <div class="py-container">
                <div class="yui3-g Logo">
                    <div class="yui3-u Left logoArea">
                        <a class="logo-bd" title="品优购" href="JD-index.html" target="_blank"></a>
                    </div>
                    <div class="yui3-u Center searchArea">
                        <div class="search">
                            <form action="<?php echo url('home/goods/index'); ?>" method="get" class="sui-form form-inline">
                                <!--searchAutoComplete-->
                                <div class="input-append">
                                    <input type="text" id="autocomplete" class="input-error input-xxlarge" name="keywords" value="<?php echo \think\Request::instance()->param('keywords'); ?>" />
                                    <button class="sui-btn btn-xlarge btn-danger" type="submit">搜索</button>
                                </div>
                            </form>
                        </div>
                        <div class="hotwords">
                            <ul>
                                <li class="f-item">品优购首发</li>
                                <li class="f-item">亿元优惠</li>
                                <li class="f-item">9.9元团购</li>
                                <li class="f-item">每满99减30</li>
                                <li class="f-item">亿元优惠</li>
                                <li class="f-item">9.9元团购</li>
                                <li class="f-item">办公用品</li>

                            </ul>
                        </div>
                    </div>
                    <div class="yui3-u Right shopArea">
                        <div class="fr shopcar">
                            <div class="show-shopcar" id="shopcar">
                                <span class="car"></span>
                                <a class="sui-btn btn-default btn-xlarge" href="<?php echo url('home/cart/index'); ?>" target="_blank">
                                    <span>我的购物车</span>
                                    <i class="shopnum">0</i>
                                </a>
                                <div class="clearfix shopcarlist" id="shopcarlist" style="display:none">
                                    <p>"啊哦，你的购物车还没有商品哦！"</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="yui3-g NavList">
                    <div class="all-sorts-list">
                        <div class="yui3-u Left all-sort">
                            <h4>全部商品分类</h4>
                        </div>
                        <div class="sort">
                            <div class="all-sort-list2">
                                <?php foreach($category as $one): if(($one['pid'])==0): ?>
                                <div class="item">
                                    <h3><a href=""><?php echo $one['cate_name']; ?></a></h3>
                                    <div class="item-list clearfix">
                                        <div class="subitem">
                                            <?php foreach($one['son'] as $two): if(($two['pid'] == $one['id'])): ?>
                                            <dl class="fore1">
                                                <dt><a href=""><?php echo $two['cate_name']; ?></a></dt>
                                                <dd>
                                                <?php foreach($two['son'] as $three): ?>
                                                    <em><a href="<?php echo url('home/goods/index',['id'=>$three['id']]); ?>"><?php echo $three['cate_name']; ?></a></em>
                                                <?php endforeach; ?>
                                                </dd>
                                            </dl>
                                            <?php endif; endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="yui3-u Center navArea">
                        <ul class="nav">
                            <li class="f-item">服装城</li>
                            <li class="f-item">美妆馆</li>
                            <li class="f-item">品优超市</li>
                            <li class="f-item">全球购</li>
                            <li class="f-item">闪购</li>
                            <li class="f-item">团购</li>
                            <li class="f-item">有趣</li>
                            <li class="f-item"><a href="seckill-index.html" target="_blank">秒杀</a></li>
                        </ul>
                    </div>
                    <div class="yui3-u Right"></div>
                </div>

            </div>
        </div>
    </div>
</div>



    <title>设置-个人信息</title>
     <link rel="icon" href="/assets/img/favicon.ico">

    <link rel="stylesheet" type="text/css" href="/static/home/css/pages-seckillOrder.css" />
	
    <div id="account">
        <div class="py-container">
            <div class="yui3-g home">
                <!--左侧列表-->
                <div class="yui3-u-1-6 list">

                    <div class="person-info">
                        <div class="person-photo"><img src="<?php echo $info['user_header']; ?>" style="width: 34px;height: 34px; border-radius: 50%
" alt=""></div>
                        <div class="person-account">
                            <span class="name"><?php echo $info['nickname']; ?></span>
                            <span class="safe">账户安全</span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="list-items">
                        <dl>
							<dt><i>·</i> 订单中心</dt>
							<dd ><a href="<?php echo url('home/order/myOrder'); ?>">我的订单</a></dd>
							<dd><a href="seckillorder-pay.html">待付款</a></dd>
							<dd><a href="seckillorder-send.html">待发货</a></dd>
							<dd><a href="seckillorder-receive.html">待收货</a></dd>
							<dd><a href="seckillorder-evaluate.html">待评价</a></dd>
						</dl>
						<dl>
							<dt><i>·</i> 我的中心</dt>
							<dd><a href="">我的收藏</a></dd>
							<dd><a href="">我的足迹</a></dd>
						</dl>
						<dl>
							<dt><i>·</i> 物流消息</dt>
						</dl>
						<dl>
							<dt><i>·</i> 设置</dt>
							<dd><a href="<?php echo url('home/user/info'); ?>" >个人信息</a></dd>
							<dd><a href="<?php echo url('home/address/address'); ?>" >地址管理</a></dd>
							<dd><a href="<?php echo url('home/user/safa'); ?>"  class="list-active">安全管理</a></dd>
						</dl>
                    </div>
                </div>
                <!--右侧主内容-->
                <div class="yui3-u-5-6">
                    <div class="body userAddress">
                        <div class="address-title">
                            <span class="title">地址管理</span>
                            <a data-toggle="modal" data-target="#addModal" data-keyboard="false"  class="sui-btn  btn-info add-new">添加新地址</a>
                            <span class="clearfix"></span>
                        </div>
                        <div class="address-detail">
                            <table class="sui-table table-bordered">
                                <thead>
                                    <tr>
                                        <th>姓名</th>
                                        <th>地址</th>
                                        <th>联系电话</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($data as $k=>$v): ?>
                                    <tr>
                                        <td><?php echo $v['consignee']; ?></td>
                                        <td><?php echo $v['area']; ?><?php echo $v['address']; ?></td>
                                        <td><?php echo $v['phone']; ?></td>
                                        <td  as="<?php echo $v['id']; ?>">
                                            <a data-toggle="modal" data-target="#editModal" data-keyboard="false"  class="edit" id="edit" gg="<?php echo $v['id']; ?>">编辑</a>

                                            <a href="<?php echo url('home/address/delete',['id'=>$v['id']]); ?>" class="sui-btn  btn-info add-new" >删除</a>
                                            <input type="submit" style="color: white; background: #5bc0de; width: 70px;height: 28px;"  class="status" value="<?php if(($v['status']==1)): ?>已默认<?php else: ?>设置默认<?php endif; ?>">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                    
                                </tbody>
                            </table>                          
                        </div>


                        <!--新增地址弹出层-->
                         <div  tabindex="-1" role="dialog" id="addModal" data-hasfoot="false" class="sui-modal hide fade" style="width:580px;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" data-dismiss="modal" aria-hidden="true" class="sui-close">×</button>
                                        <h4 id="myModalLabel" class="modal-title">新增地址</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" class="sui-form form-horizontal" id="newForm">
                                            <input type="hidden" name="site" value="">
                                            <div class="control-group">
                                            <label class="control-label">收货人：</label>
                                            <div class="controls">
                                                <input type="text" class="input-medium" name="consignee">
                                            </div>
                                        </div>
                                        <div class="control-group" id="citry">
                                            <label class="control-label">所在地区：</label>
                                            <div class="controls">
                                                <div data-toggle="distpicker" id="focus">
                                                <div class="form-group area">
                                                    <select class="form-control" id="province1" name="sh"></select>
                                                </div>
                                                <div class="form-group area">
                                                    <select class="form-control" id="city1" name="shi"></select>
                                                </div>
                                                <div class="form-group area">
                                                    <select class="form-control" id="district1" name="qu"></select>
                                                </div>
                                            </div>
                                            </div>									 
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">详细地址：</label>
                                            <div class="controls">
                                                <input type="text" class="input-large" name="address">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">联系电话：</label>
                                            <div class="controls">
                                                <input type="text" class="input-medium" name="phone">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">邮箱：</label>
                                            <div class="controls">
                                                <input type="text" class="input-medium" name="email">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">地址别名：</label>
                                            <div class="controls">
                                                <input type="text" class="input-medium">
                                            </div>
                                            <div class="othername">
                                                建议填写常用地址：<a href="#" class="sui-btn btn-default">家里</a>　<a href="#" class="sui-btn btn-default">父母家</a>　<a href="#" class="sui-btn btn-default">公司</a>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-ok="modal" class="sui-btn btn-primary btn-large" id="ok">确定</button>
                                        <button type="button" data-dismiss="modal" class="sui-btn btn-default btn-large">取消</button>
                                    </div>
                                </div>
                            </div>
						</div>



                        <div  tabindex="-1" role="dialog" id="editModal" data-hasfoot="false" class="sui-modal hide fade" style="width:580px;">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" data-dismiss="modal" aria-hidden="true" class="sui-close">×</button>
                                        <h4 class="modal-title">修改地址</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" class="sui-form form-horizontal" id="saveForm">
                                            <input type="hidden" name="site" value="">
                                            <div class="control-group">
                                                <label class="control-label">收货人：</label>
                                                <div class="controls">
                                                    <input type="text" class="input-medium" id="consignee" name="consignee" value="">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">所在地区：</label>
                                                <div class="controls">
                                                    <div id="distpicker">
                                                        <div class="form-group area">
                                                            <select class="form-control" id="province2" name="city"></select>
                                                        </div>
                                                        <div class="form-group area">
                                                            <select class="form-control" id="city2" name="shi"></select>
                                                        </div>
                                                        <div class="form-group area">
                                                            <select class="form-control" id="district2" name="qu"></select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">详细地址：</label>
                                                <div class="controls">
                                                    <input type="text" class="input-large" name="address" id="address" value="">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">联系电话：</label>
                                                <div class="controls">
                                                    <input type="text" class="input-medium" id="phone" name="phone" value="">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">邮箱：</label>
                                                <div class="controls">
                                                    <input type="text" class="input-medium" name="email" id="email" value="">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">地址别名：</label>
                                                <div class="controls">
                                                    <input type="text" class="input-medium">
                                                </div>
                                                <div class="othername">
                                                    建议填写常用地址：<a href="#" class="sui-btn btn-default">家里</a>　<a href="#" class="sui-btn btn-default">父母家</a>　<a href="#" class="sui-btn btn-default">公司</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-ok="modal" class="sui-btn btn-primary btn-large" id="save">确定</button>
                                        <button type="button" data-dismiss="modal" class="sui-btn btn-default btn-large">取消</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            // 获取 所在地区的值
            var site=$('#province1').val()+"_"+$('#city1').val()+"_"+$('#district1').val();
            $('input[name=site]').val(site);
            $('#ok').click(function () {
                $.ajax({
                    'url':'<?php echo url("home/address/updates"); ?>',
                    'type':'post',
                    'data':$('#newForm').serialize(),
                    'dataType':'json',
                    'success':function (res) {
                        if(res.code==200){
                        alert("添加成功");
                            location.reload();
                        }else{
                            alert("添加失败");
                        }
                    }
                })
            });
            // 点击编辑 发送ajax  渲染页面的使用
            $('.edit').click(function () {
                var as=$(this).attr('gg');
                // console.log(as);
                // alert('11');
                $.ajax({
                   'url':"<?php echo url('home/address/edit'); ?>",
                    'type':'get',
                    'data':as,
                    'success':function (res) {
                        if(res.code==200){
                            var data=res.data;
                            for(var i in data){
                                   // console.log(data[i]);
                                console.log(data.area[0]);
                                // console.log(res.data);
                                // html +=data.phone;
                                $('#consignee').val(data.consignee);
                                $('#address').val(data.address);
                                $('#phone').val(data.phone);
                                $('#email').val(data.email);
                            }
                            // console.log(data);
                        }
                                $('#distpicker').distpicker('destroy').distpicker({
                                    province:data.area[0],
                                    city:data.area[1],
                                    district:data.area[2],
                                })
                    }
                });
            });
            // 选中默认的js 效果
            $('.status').click(function () {
                var ss=$(this).parent().attr('as');
                // var x=$(this).val("默认");
                // console.log(ss);
                var that = this;
                $.ajax({
                    'url':"<?php echo url('home/address/status'); ?>",
                    'type':"get",
                    'data':ss,
                    'dataType':"json",
                    'success':function (res) {
                        if(res.code==200){
                            // 为选中的状态 //
                            $('.that').val("设置默认");
                            alert("设置默认成功");
                            location.reload();
                        }
                        if(res.code==201){
                            // 为没选中状态
                            $('.that').val("默认");
                            alert("取消默认成功");
                            location.reload();
                        }
                    }
                });
            });

            // 发送ajax 做点击确定编辑以后的数据处理
            $('#save').click(function () {
                $.ajax({
                   'url':"<?php echo url('home/address/save'); ?>",
                   'type':"post",
                   "data":$('#saveForm').serialize(),
                   'dataType':'json',
                   'success':function (res) {
                        if(res.code==200){
                            alert(res.msg);
                            location.reload();
                        }else{
                            alert(res.msg);
                        }
                   } 
                });
            })

        })
    </script>

	
<script type="text/javascript" src="/static/home/js/plugins/citypicker/distpicker.data.js"></script>
<script type="text/javascript" src="/static/home/js/plugins/citypicker/distpicker.js"></script>
<script type="text/javascript" src="/static/home/js/pages/main.js"></script>
</body>

</html>


<!-- 底部栏位 -->
<!--页面底部-->
<div class="clearfix footer">
    <div class="py-container">
        <div class="footlink">
            <div class="Mod-service">
                <ul class="Mod-Service-list">
                    <li class="grid-service-item intro  intro1">

                        <i class="serivce-item fl"></i>
                        <div class="service-text">
                            <h4>正品保障</h4>
                            <p>正品保障，提供发票</p>
                        </div>

                    </li>
                    <li class="grid-service-item  intro intro2">

                        <i class="serivce-item fl"></i>
                        <div class="service-text">
                            <h4>正品保障</h4>
                            <p>正品保障，提供发票</p>
                        </div>

                    </li>
                    <li class="grid-service-item intro  intro3">

                        <i class="serivce-item fl"></i>
                        <div class="service-text">
                            <h4>正品保障</h4>
                            <p>正品保障，提供发票</p>
                        </div>

                    </li>
                    <li class="grid-service-item  intro intro4">

                        <i class="serivce-item fl"></i>
                        <div class="service-text">
                            <h4>正品保障</h4>
                            <p>正品保障，提供发票</p>
                        </div>

                    </li>
                    <li class="grid-service-item intro intro5">

                        <i class="serivce-item fl"></i>
                        <div class="service-text">
                            <h4>正品保障</h4>
                            <p>正品保障，提供发票</p>
                        </div>

                    </li>
                </ul>
            </div>
            <div class="clearfix Mod-list">
                <div class="yui3-g">
                    <div class="yui3-u-1-6">
                        <h4>购物指南</h4>
                        <ul class="unstyled">
                            <li>购物流程</li>
                            <li>会员介绍</li>
                            <li>生活旅行/团购</li>
                            <li>常见问题</li>
                            <li>购物指南</li>
                        </ul>

                    </div>
                    <div class="yui3-u-1-6">
                        <h4>配送方式</h4>
                        <ul class="unstyled">
                            <li>上门自提</li>
                            <li>211限时达</li>
                            <li>配送服务查询</li>
                            <li>配送费收取标准</li>
                            <li>海外配送</li>
                        </ul>
                    </div>
                    <div class="yui3-u-1-6">
                        <h4>支付方式</h4>
                        <ul class="unstyled">
                            <li>货到付款</li>
                            <li>在线支付</li>
                            <li>分期付款</li>
                            <li>邮局汇款</li>
                            <li>公司转账</li>
                        </ul>
                    </div>
                    <div class="yui3-u-1-6">
                        <h4>售后服务</h4>
                        <ul class="unstyled">
                            <li>售后政策</li>
                            <li>价格保护</li>
                            <li>退款说明</li>
                            <li>返修/退换货</li>
                            <li>取消订单</li>
                        </ul>
                    </div>
                    <div class="yui3-u-1-6">
                        <h4>特色服务</h4>
                        <ul class="unstyled">
                            <li>夺宝岛</li>
                            <li>DIY装机</li>
                            <li>延保服务</li>
                            <li>品优购E卡</li>
                            <li>品优购通信</li>
                        </ul>
                    </div>
                    <div class="yui3-u-1-6">
                        <h4>帮助中心</h4>
                        <img src="/static/home/img/wx_cz.jpg">
                    </div>
                </div>
            </div>
            <div class="Mod-copyright">
                <ul class="helpLink">
                    <li>关于我们<span class="space"></span></li>
                    <li>联系我们<span class="space"></span></li>
                    <li>关于我们<span class="space"></span></li>
                    <li>商家入驻<span class="space"></span></li>
                    <li>营销中心<span class="space"></span></li>
                    <li>友情链接<span class="space"></span></li>
                    <li>关于我们<span class="space"></span></li>
                    <li>营销中心<span class="space"></span></li>
                    <li>友情链接<span class="space"></span></li>
                    <li>关于我们</li>
                </ul>
                <p>地址：北京市昌平区建材城西路金燕龙办公楼一层 邮编：100096 电话：400-618-4000 传真：010-82935100</p>
                <p>京ICP备08001421号京公网安备110108007702</p>
            </div>
        </div>
    </div>
</div>
<!--页面底部END-->
<!--侧栏面板开始-->
<div class="J-global-toolbar">
    <div class="toolbar-wrap J-wrap">
        <div class="toolbar">
            <div class="toolbar-panels J-panel">

                <!-- 购物车 -->
                <div style="visibility: hidden;" class="J-content toolbar-panel tbar-panel-cart toolbar-animate-out">
                    <h3 class="tbar-panel-header J-panel-header">
                        <a href="" class="title"><i></i><em class="title">购物车</em></a>
                        <span class="close-panel J-close" onclick="cartPanelView.tbar_panel_close('cart');" ></span>
                    </h3>
                    <div class="tbar-panel-main">
                        <div class="tbar-panel-content J-panel-content">
                            <div id="J-cart-tips" class="tbar-tipbox hide">
                                <div class="tip-inner">
                                    <span class="tip-text">还没有登录，登录后商品将被保存</span>
                                    <a href="#none" class="tip-btn J-login">登录</a>
                                </div>
                            </div>
                            <div id="J-cart-render">
                                <!-- 列表 -->
                                <div id="cart-list" class="tbar-cart-list">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 小计 -->
                    <div id="cart-footer" class="tbar-panel-footer J-panel-footer">
                        <div class="tbar-checkout">
                            <div class="jtc-number"> <strong class="J-count" id="cart-number">0</strong>件商品 </div>
                            <div class="jtc-sum"> 共计：<strong class="J-total" id="cart-sum">¥0</strong> </div>
                            <a class="jtc-btn J-btn" href="#none" target="_blank">去购物车结算</a>
                        </div>
                    </div>
                </div>

                <!-- 我的关注 -->
                <div style="visibility: hidden;" data-name="follow" class="J-content toolbar-panel tbar-panel-follow">
                    <h3 class="tbar-panel-header J-panel-header">
                        <a href="#" target="_blank" class="title"> <i></i> <em class="title">我的关注</em> </a>
                        <span class="close-panel J-close" onclick="cartPanelView.tbar_panel_close('follow');"></span>
                    </h3>
                    <div class="tbar-panel-main">
                        <div class="tbar-panel-content J-panel-content">
                            <div class="tbar-tipbox2">
                                <div class="tip-inner"> <i class="i-loading"></i> </div>
                            </div>
                        </div>
                    </div>
                    <div class="tbar-panel-footer J-panel-footer"></div>
                </div>

                <!-- 我的足迹 -->
                <div style="visibility: hidden;" class="J-content toolbar-panel tbar-panel-history toolbar-animate-in">
                    <h3 class="tbar-panel-header J-panel-header">
                        <a href="#" target="_blank" class="title"> <i></i> <em class="title">我的足迹</em> </a>
                        <span class="close-panel J-close" onclick="cartPanelView.tbar_panel_close('history');"></span>
                    </h3>
                    <div class="tbar-panel-main">
                        <div class="tbar-panel-content J-panel-content">
                            <div class="jt-history-wrap">
                                <ul>
                                    <!--<li class="jth-item">
                                        <a href="#" class="img-wrap"> <img src="/static/home//static/home/./static/home/portal/img/like_03.png" height="100" width="100" /> </a>
                                        <a class="add-cart-button" href="#" target="_blank">加入购物车</a>
                                        <a href="#" target="_blank" class="price">￥498.00</a>
                                    </li>
                                    <li class="jth-item">
                                        <a href="#" class="img-wrap"> <img src="/static/home//static/home//static/home/portal/img/like_02.png" height="100" width="100" /></a>
                                        <a class="add-cart-button" href="#" target="_blank">加入购物车</a>
                                        <a href="#" target="_blank" class="price">￥498.00</a>
                                    </li>-->
                                </ul>
                                <a href="#" class="history-bottom-more" target="_blank">查看更多足迹商品 &gt;&gt;</a>
                            </div>
                        </div>
                    </div>
                    <div class="tbar-panel-footer J-panel-footer"></div>
                </div>

            </div>

            <div class="toolbar-header"></div>

            <!-- 侧栏按钮 -->
            <div class="toolbar-tabs J-tab">
                <div onclick="cartPanelView.tabItemClick('cart')" class="toolbar-tab tbar-tab-cart" data="购物车" tag="cart" >
                    <i class="tab-ico"></i>
                    <em class="tab-text"></em>
                    <span class="tab-sub J-count " id="tab-sub-cart-count">0</span>
                </div>
                <div onclick="cartPanelView.tabItemClick('follow')" class="toolbar-tab tbar-tab-follow" data="我的关注" tag="follow" >
                    <i class="tab-ico"></i>
                    <em class="tab-text"></em>
                    <span class="tab-sub J-count hide">0</span>
                </div>
                <div onclick="cartPanelView.tabItemClick('history')" class="toolbar-tab tbar-tab-history" data="我的足迹" tag="history" >
                    <i class="tab-ico"></i>
                    <em class="tab-text"></em>
                    <span class="tab-sub J-count hide">0</span>
                </div>
            </div>

            <div class="toolbar-footer">
                <div class="toolbar-tab tbar-tab-top" > <a href="#"> <i class="tab-ico  "></i> <em class="footer-tab-text">顶部</em> </a> </div>
                <div class="toolbar-tab tbar-tab-feedback" > <a href="#" target="_blank"> <i class="tab-ico"></i> <em class="footer-tab-text ">反馈</em> </a> </div>
            </div>

            <div class="toolbar-mini"></div>

        </div>

        <div id="J-toolbar-load-hook"></div>

    </div>
</div>
<!--购物车单元格 模板-->
<script type="text/template" id="tbar-cart-item-template">
    <div class="tbar-cart-item" >
        <div class="jtc-item-promo">
            <em class="promo-tag promo-mz">满赠<i class="arrow"></i></em>
            <div class="promo-text">已购满600元，您可领赠品</div>
        </div>
        <div class="jtc-item-goods">
            <span class="p-img"><a href="#" target="_blank"><img src="{2}" alt="{1}" height="50" width="50" /></a></span>
            <div class="p-name">
                <a href="#">{1}</a>
            </div>
            <div class="p-price"><strong>¥{3}</strong>×{4} </div>
            <a href="#none" class="p-del J-del">删除</a>
        </div>
    </div>
</script>
<!--侧栏面板结束-->

</body>

</html>