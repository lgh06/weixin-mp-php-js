<?php
require_once dirname(__FILE__).'/actoken.php';
require_once dirname(__FILE__).'/db/db_mysql.class.php';

/**
 *访问地址
 * https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxab18db1751df337d&redirect_uri=http%3a%2f%2flgh92.eicp.net%2fzhuboadmin%2fweixin%2fgetcode.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
 */

define("TOKEN", "If985hike");
define("APPID", "wxab18db1751df337d");

define("APPSECRET","5ef818a1786bc5900e5ca0cde4576490");

define("ACCESS_TOKEN",returnAccessToken());
//测试号访问地址https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxe9828da2665d3b63&redirect_uri=http%3a%2f%2flgh92.eicp.net%2fzhuboadmin%2fweixin%2fgetcode.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect

/**
 * 生成随机字符串
 * @param int $pw_length
 * @return string
 */
function create_rand($pw_length = 6)
{
    $randpwd = '';
for ($i = 0; $i < $pw_length; $i++)
{
    $randpwd .= chr(mt_rand(97, 122));
}
return $randpwd;
}

class wechatCallbackapiTest
{
    //验证消息
    public function valid()
    {
        @$echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    //检查签名
    private function checkSignature()
    {
        @$signature = $_REQUEST["signature"];
        @$timestamp = $_REQUEST["timestamp"];
        @$nonce = $_REQUEST["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }

    //响应消息
    public function responseMsg()
    {
        @$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknow msg type: ".$RX_TYPE;
                    break;
            }
            $this->logger("T ".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    //接收事件消息
    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
                $content = "金池贷欢迎您的关注！\n请输入数字来了解详情：\n"
				."1.关于我们\n"
				."2.了解P2P互联网金融\n"
				."3.其它信息，请浏览电脑版网站\n"
				."<a href=\"http://www.jinchidai.com\">金池e贷</a>";
                $content .= (!empty($object->EventKey))?("\n来自二维码场景 ".str_replace("qrscene_","",$object->EventKey)):"";
                break;
            case "unsubscribe":
                $content = "取消关注";
                break;
            case "SCAN":
                $content = "扫描场景 ".$object->EventKey;
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "gsjj":
                        $content = "金池e贷(www.jinchidai.com)是天津金池投资管理有限公司旗下全资独立品牌，成立于2012年9月。\n金池e贷作为天津本地首家专业级的网络借贷平台，本着诚信、尽责、专业的原则，主要为天津本地的个人消费贷款、小微型企业的融资提供专业化、快速、安全的服务，为创造本地就业、有效盘活民间闲置资金、解决小微型企业融资难等问题提供帮助。\n金池e贷拥有一支精通专业知识、互联网技术和政策法规的专业队伍，能为出借双方提供专业的经济信息服务咨询，能有效规避风险、规范运营，为天津本地的经济发展做出积极贡献。\n更多详情，请查看<a href=\"http://www.jinchidai.com/\">金池e贷</a>";
                        break;
					case "lxwm":
						$content ="P2P互联网金融为您提供融资、理财新方式。\n金池e贷竭诚为您服务。\n400热线：400 839 3089\n客服：\n022-83281721\n邮箱：\njinchidai@163.com\n招聘邮箱：\njinchihr@163.com\n更多详情，请查看<a href=\"http://www.jinchidai.com/\">金池e贷</a>";
						break;
					case "czfs":
						$content ="充值方式:\n1.电脑线上充值(双乾支付)\n\n2.人工充值:\n账户姓名:杨魁英\n开户银行:招商银行 天津滨海分行营业厅\n卡号:6214 8526 0007 6817\n\n账户姓名:杨魁英\n开户银行:中国农业银行 天津分行 绍兴道支行\n卡号:622845 002800 5595977\n\n账户姓名:杨魁英\n开户银行:中国工商银行 天津分行 新村佳园里支行\n卡号:622202 0302079006623\n\n账户姓名：杨魁英\n支付宝账户：\n2256121579@qq.com\n\n更多详情，请查看<a href=\"http://www.jinchidai.com/\">金池e贷</a>";
						break;
					default:
                        $content = "点击菜单：".$object->EventKey;
                        break;
                }
                break;
            case "LOCATION":
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;
            case "VIEW":
                $content = "跳转链接 ".$object->EventKey;
                break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收文本消息
    private function receiveText($object)
    {
		
        switch ($object->Content)
        {
			case "文本":
                $content = "hahaha";
                break;
/*             case "图文":
            case "单图文":
                $content = array();
                $content[] = array(
				"Title"=>"单图文标题",
				"Description"=>"单图文内容",
				"PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
				"Url" =>"http://m.cnblogs.com/?u=txw1958"
				);
                break;
            case "多图文":
                $content = array();
                $content[] = array(
				"Title"=>"多图文1标题",
				"Description"=>"",
				"PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg",
				"Url" =>"http://m.cnblogs.com/?u=txw1958"
				);
                $content[] = array(
				"Title"=>"多图文2标题",
				"Description"=>"",
				"PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg",
				"Url" =>"http://m.cnblogs.com/?u=txw1958"
				);
                $content[] = array(
				"Title"=>"多图文3标题",
				"Description"=>"",
				"PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg",
				"Url" =>"http://m.cnblogs.com/?u=txw1958"
				);
                break;
            case "音乐":
                $content = array(
				"Title"=>"最炫民族风",
				"Description"=>"歌手：凤凰传奇",
				"MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3",
				"HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3"
				);
                break; */
			default:{
			
				$dbc = new db_mysql($GLOBALS['db_conf']);
				$content="";
                
				$re = $dbc->selectSQL("select content,newsids from wx_reply where id={$object->Content}");
				if($re!=false){				
					if(empty($re['content'])&&!empty($re['newsids'])){
					$content = $dbc->getManyNews($re['newsids']);
					}				
					if(!empty($re['content'])) {$content = $re['content'];}
				}
				if(empty($content)){
				$content="您的意见已记录。我们会尽快回复。谢谢。";
				//TODO 存入数据库
				}
				
				//$content .= date("Y-m-d H:i:s",time());
				
				
                break;
					}
        }
        if(is_array($content)){//判断是否是数组
            if (isset($content[0]['PicUrl'])){//是否是图文
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){//是否是音乐
                $result = $this->transmitMusic($object, $content);
            }
        }else{//只是文字
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }

    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
</Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[]]></Content>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 10000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }
	
}

function http_request($url)	
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);//需要获取的URL地址，也可以在curl_init()函数中设置。 
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);//禁用后cURL将终止从服务端进行验证。使用CURLOPT_CAINFO选项设置证书使用CURLOPT_CAPATH选项设置证书目录 如果CURLOPT_SSL_VERIFYPEER(默认值为2)被启用，CURLOPT_SSL_VERIFYHOST需要被设置成TRUE否则设置为FALSE。 
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_HTTPGET, TRUE);//默认即为GET请求        
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//将exec执行结果以文件流形式返回不是直接输出到浏览器
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
	
function https_request($url,$data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);//需要获取的URL地址，也可以在curl_init()函数中设置。 
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);//禁用后cURL将终止从服务端进行验证。使用CURLOPT_CAINFO选项设置证书使用CURLOPT_CAPATH选项设置证书目录 如果CURLOPT_SSL_VERIFYPEER(默认值为2)被启用，CURLOPT_SSL_VERIFYHOST需要被设置成TRUE否则设置为FALSE。 
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_HTTPGET, TRUE);//默认即为GET请求
    if (!empty($data))
	{
        curl_setopt($curl, CURLOPT_POST, TRUE);//启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);//全部数据使用HTTP协议中的"POST"操作来发送
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//将exec执行结果以文件流形式返回不是直接输出到浏览器
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}