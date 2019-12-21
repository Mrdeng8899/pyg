<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
if(!function_exists('encrypt_password')){
    // 密码加密函数
    function encrypt_password($password){
        $salt=md5(md5('innn21455'.$password));
        return md5($salt.md5($password));
    }
}
if (!function_exists('get_cate_list')) {
    //递归函数 实现无限级分类列表
    function get_cate_list($list,$pid=0,$level=0) {
        static $tree = array();
        foreach($list as $row) {
            if($row['pid']==$pid) {
                $row['level'] = $level;
                $tree[] = $row;
                get_cate_list($list, $row['id'], $level + 1);
            }
        }
        return $tree;
    }
}

if(!function_exists('get_tree_list')){
    //引用方式实现 父子级树状结构
    function get_tree_list($list){
        //将每条数据中的id值作为其下标
        $temp = [];
        foreach($list as $v){
            $v['son'] = [];
            $temp[$v['id']] = $v;
        }
        //获取分类树
        foreach($temp as $k=>$v){
            $temp[$v['pid']]['son'][] = &$temp[$v['id']];
        }
        return isset($temp[0]['son']) ? $temp[0]['son'] : [];
    }
}

if (!function_exists('remove_xss')) {
    //使用htmlpurifier防范xss攻击
    function remove_xss($string){
        //composer安装的，不需要此步骤。相对index.php入口文件，引入HTMLPurifier.auto.php核心文件
//         require_once './plugins/htmlpurifier/HTMLPurifier.auto.php';
        // 生成配置对象
        $cfg = HTMLPurifier_Config::createDefault();
        // 以下就是配置：
        $cfg -> set('Core.Encoding', 'UTF-8');
        // 设置允许使用的HTML标签
        $cfg -> set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,br,p[style],span[style],img[width|height|alt|src]');
        // 设置允许出现的CSS样式属性
        $cfg -> set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
        // 设置a标签上是否允许使用target="_blank"
        $cfg -> set('HTML.TargetBlank', TRUE);
        // 使用配置生成过滤用的对象
        $obj = new HTMLPurifier($cfg);
        // 过滤字符串
        return $obj -> purify($string);
    }
    if(!function_exists('encrypted_phone')){
        function encrypted_phone($phone){
            return substr($phone,0,3).'****'.substr($phone,7,4);
        }
    }


    if(!function_exists('curl_request')){
        // 使用curl函数库 发送请求
        function curl_request($url,$post=false,$params=[],$https=false){
            //初始化请求会话
            $ch=curl_init($url);
            // 设置请求选项
            // 请求方式 默认curl发送get请求
            if($post){
                // 设置发送post请求
                curl_setopt($ch,CURLOPT_POST,true);
                // 设置post请求参数
                curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
            }
            // https协议是否验证证书
            if($https){
                // 禁止从服务器端验证客户端bending的证书
                curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            }
            // 发送请求
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            $res=curl_exec($ch);
            if($res === false){
                $msg=curl_error($ch);
                return [$msg];
            }
            // 关闭请求
            curl_close($ch);
            return $res;
        }
    }



    // 封装短信 用户发送短信的
    if(!function_exists('send_msg')){
        function send_msg($phone,$msg){
            // 请求地址
            $url=config('msg.gateway');
            $appkey=config('msg.appkey');
            // 请求参数
            $url .='?appkey='.$appkey.'&mobile='.$phone.'&content='.$msg;
            // post请求 参数也必须放在url中
            $res=curl_request($url,true,[],true);
            if(is_array($res)){
                // 请求发送失败 请求没有发出去
                return $res[0];
            }
            // 解析结果字符集
            $arr=json_decode($res,true);
//            dump($arr);die;
            if(!isset($arr['code'])||$arr['code'] !=10000){
                return "短信接口请求失败";
            }
            if(!isset($arr['result']['ReturnStatus']) || $arr['result']['ReturnStatus'] !='Success'){
                return "短信发送失败";
            }
            return true;
        }
    }


    // 发送邮件
    if(!function_exists('send_email')){
        function send_email($to,$body,$subject){
            require '../extend/email/class.phpmailer.php';
            $mail= new PHPMailer();
            // 提取配置文件信息
            $server=config('email_server');
            /*服务器相关信息*/
            $mail->IsSMTP();   //启用smtp服务发送邮件
            $mail->SMTPAuth   = true;  //设置开启认证
            $mail->Host       = $server["host"];   	 //指定smtp邮件服务器地址
            $mail->Username   = $server["user"];  	//指定用户名
            $mail->Password   = $server["password"];    //邮箱的第三方客户端的授权密码
            /*内容信息*/
            $mail->IsHTML(true);
            $mail->CharSet    ="UTF-8";
            $mail->From       = $server["email_address"];
            $mail->FromName   =$server["nickname"];	//发件人昵称
            $mail->Subject    = $subject;                               //发件主题
            $mail->MsgHTML($body);	                                    //邮件内容 支持HTML代码
            $mail->AddAddress($to);                                     //收件人邮箱地址
            //$mail->AddAttachment("test.png"); //附件
            $mail->Send();			//发送邮箱
        }
    }

}

