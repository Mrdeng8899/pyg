<?php
	require 'class.phpmailer.php';
	$mail             = new PHPMailer();
	/*服务器相关信息*/
	$mail->IsSMTP();   //启用smtp服务发送邮件                     
	$mail->SMTPAuth   = true;  //设置开启认证             
	$mail->Host       = 'smtp.163.com';   	 //指定smtp邮件服务器地址  
	$mail->Username   = 'deng88998090';  	//指定用户名
	$mail->Password   = 'deng88998090';		//邮箱的第三方客户端的授权密码
	/*内容信息*/
	$mail->IsHTML(true);
	$mail->CharSet    ="UTF-8";			
	$mail->From       = 'deng88998090@163.com';   // 写自己的邮箱里面配置的那个东西
	$mail->FromName   ="发大发";	//发件人昵称
	$mail->Subject    = '范德萨范德萨发'; //发件主题
	$mail->MsgHTML('阿富汗的萨克结了婚放得开萨拉赫放得开来撒会反馈到拉萨发快递号萨芬');	//邮件内容 支持HTML代码
   

	$mail->AddAddress('baoshuai2019@163.com');  //收件人邮箱地址 收件人的邮箱
	//$mail->AddAttachment("test.png"); //附件  就是你要发送图片 文件之类的东西
	$mail->Send();			//发送邮箱 这个是发送邮件的方法
?>