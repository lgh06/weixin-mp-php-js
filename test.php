<?php
require_once dirname(__FILE__).'/common/base.php';
$wechatObj = new wechatCallbackapiTest();
if(!isset($_GET["echostr"])) {
	$wechatObj->responseMsg();   
}else{
	$wechatObj->valid();
}