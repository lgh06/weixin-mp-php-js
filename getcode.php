<?php
require_once dirname(__FILE__).'/common/base.php';
/**
 *
 * 访问地址
 * https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxab18db1751df337d&redirect_uri=http%3a%2f%2flgh92.eicp.net%2fzhuboadmin%2fweixin%2fgetcode.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
 *
 * 文档地址：
 * http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html
 *
 * */
?>
<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8"/>
        <title>滴答清单</title>
        <style>
            html,body{
                height: 100%;
                width: 100%;
            }
        </style>
        <script src="//lib.sinaapp.com/js/jquery/1.10.2/jquery-1.10.2.min.js"></script>
        <script src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <script>
            <?php
                $time = time();
                $nonceStr = create_rand();
                $signature = "jsapi_ticket=";
                $signature .= returnJsapiTicket();
                $signature .= "&noncestr={$nonceStr}";
                $signature .= "&timestamp={$time}";
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off")?"https":"http";
                $signature .= "&url={$protocol}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";


            ?>

            wx.config({
                debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                appId: 'wxe9828da2665d3b63', // 必填，公众号的唯一标识
                timestamp: '<?php echo $time; ?>', // 必填，生成签名的时间戳
                nonceStr: '<?php echo $nonceStr; ?>', // 必填，生成签名的随机串
                signature: '<?php echo sha1($signature); ?>',// 必填，签名，见附录1
                jsApiList: ["openLocation",
                    "getLocation",
                    "showOptionMenu",
                    "startRecord",//开始录音
                    "stopRecord",//停止录音
                    "onVoiceRecordEnd",//自动停止长时间录音
                    "playVoice",
                    "pauseVoice",
                    "stopVoice"
                ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
            });
            jQuery(function(){
                var latitude = 0,longitude = 0;
                wx.ready(function(){
                    wx.showOptionMenu();
                    wx.getLocation({
                        success: function (res) {
                            console.log("1111"+res);
                            //alert(res);
                            latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                            longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                            var speed = res.speed; // 速度，以米/每秒计
                            var accuracy = res.accuracy; // 位置精度
                        }
                    });
                    wx.checkJsApi({
                        jsApiList: ["openLocation",
                            "getLocation",
                            "showOptionMenu",
                            "startRecord",//开始录音
                            "stopRecord",//停止录音
                            "onVoiceRecordEnd",//自动停止长时间录音
                            "playVoice",
                            "pauseVoice",
                            "stopVoice"
                        ], // 需要检测的JS接口列表，所有JS接口列表见附录2,
                        success: function(res) {
                           console.log("2222"+res);
                        }
                    });
/*                    window.onclick = function(){
                        wx.openLocation({
                            latitude: latitude, // 纬度，浮点数，范围为90 ~ -90
                            longitude: longitude, // 经度，浮点数，范围为180 ~ -180。
                            name: '', // 位置名
                            address: '', // 地址详情说明
                            scale: 1, // 地图缩放级别,整形值,范围从1~28。默认为最大
                            infoUrl: '' // 在查看位置界面底部显示的超链接,可点击跳转
                        });
                    }*/

                    wx.onVoiceRecordEnd({
                        // 录音时间超过一分钟没有停止的时候会执行 complete 回调
                        complete: function (res) {
                            var localId = res.localId;
                        }
                    });

                });


            });
        </script>

    </head>
    <body>
<?php




//此时已经跳转到公司服务器，需要记录微信服务器的code
$code = $_REQUEST["code"];



/*
 * 将code存在本地code.php中
 * $c = file_get_contents('code.php');

if(	true||!file_exists('code.php') || (time()-filemtime('code.php')>7100)||empty($c))
{

    file_put_contents('code.php',$code);//用ac.php文件内的字符串做缓存
    clearstatcache();
}*/

//公司服务器请求微信服务器，获取access_token

$actokenUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".APPID."&secret=".APPSECRET."&code=".$code."&grant_type=authorization_code";
$res = https_request($actokenUrl);//记录access_token

$jsonArray = json_decode($res,true);
$actoken = $jsonArray["access_token"];
$openid = $jsonArray["openid"];

//TODO refresh token

//获取用户信息
$userInfoUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=".$actoken."&openid=".$openid."&lang=zh_CN";
$userInfo = https_request($userInfoUrl);
var_dump($userInfo);
?>

    <button id="startRecord">开始</button>
    <button id="stopRecord">停止</button>
    <br/>
    <button id="playVoice">播放</button>
    <button id="pauseVoice">暂停</button>
    <button id="stopVoice">停止</button>
<script>
    var currMediaId = "";
    jQuery(document).ready(

        function ($) {

            $("#startRecord").click(function () {
                wx.startRecord();


            });
            $("#stopRecord").click(function () {
                wx.stopRecord({
                    success: function (res) {
                        currMediaId = res.localId;
                    }
                });
            });
            $("#playVoice").click(function () {
                wx.playVoice({
                    localId: currMediaId // 需要播放的音频的本地ID，由stopRecord接口获得
                });
            });

            $("#pauseVoice").click(function () {
                wx.pauseVoice({
                    localId: currMediaId // 需要播放的音频的本地ID，由stopRecord接口获得
                });
            });

            $("#stopVoice").click(function () {
                wx.stopVoice({
                    localId: currMediaId // 需要播放的音频的本地ID，由stopRecord接口获得
                });
            });

            function hasGetUserMedia() {
                //请注意:在Opera浏览器中不使用前缀
                return !!(navigator.getUserMedia || navigator.webkitGetUserMedia ||
                navigator.mozGetUserMedia || navigator.msGetUserMedia);
            }
            if (hasGetUserMedia()) {
                alert('您的浏览器支持getUserMedia方法');
            }
            else {
                alert('您的浏览器不支持getUserMedia方法');
            }



        }
    );
</script>
    </body>
</html>

