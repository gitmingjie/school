<?php 
require_once __DIR__ .'/WeixinConfig.class.php';
//Î¢ÐÅÊÚÈ¨Í³Ò»µ÷¶È
class WxAuth{
    //ÑéÖ¤Ç©Ãû
    //$data Ò»°ãÊÇ$_GET
    public function checkSignature($data)
    {
        $msgSignature = $data["signature"]?? '';
        if(!$msgSignature) return false;
        $data= array(
            'token'=>WeixinConfig::TOKEN,
            'timestamp'=>$data["timestamp"]?? '',
            'nonce'=>$data["nonce"]?? ''           
        );            
        sort($data,SORT_STRING);//×ÖµäÅÅÐò
        $signature =   sha1(join('',$data));   
        return $signature == $msgSignature;
    }
    
    //»ñÈ¡È«¾Öaccess_token
    public function getAccessToken(){
        $access_token = RD()->get('access_token');
        if(!$access_token){
            $url  = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&";
            $arr = array(
                'appid'=>APPID,
                'secret'=>APPSECRET,        
            ); 
            $url .= http_build_query($arr);     
            $json = $this->curl($url);
            $arr = json_decode($json,true);          
            $access_token = $arr['access_token'] ?? null;        
            $expires_in = $arr['expires_in'] ?? 0;
            if( $access_token && $expires_in){  
                RD()->set('access_token',$access_token,$expires_in - 1800);
            }
        }        
        return $access_token;
    }
    //»ñÈ¡ÊÚÈ¨Á¬½Ó,·µ»ØÒ»¸öCODE
    //https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455784140&token=&lang=zh_CN
    public function getAuthUrl($retutn_url,$scope='snsapi_base',$state=1){
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        $arr = array(
           'appid'=>APPID,
           'redirect_uri'=>$retutn_url,
           'response_type'=>'code',
           'scope'=>$scope,
           'state'=>$state  
        );    
        return $url . http_build_query($arr) . '#wechat_redirect';        
    }
   //获取openid
    public function getOpenid($code){        
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';       
        $arr = array(
           'appid'=>APPID,
           'secret'=>APPSECRET,
           'code'=>$code,
           'grant_type'=>'authorization_code',       
        );   
        $url .= http_build_query($arr); 
        $json = $this->curl($url);
        $arr = json_decode($json,true);
        if(!empty($arr['openid'])){
            return $arr;
        }    
        return null;        
    }
    //Ë¢ÐÂÓÃ»§token
    //refresh_token  getOpenid»ñÈ¡µÄrefresh_token
    public function getNewToken($refresh_token){
        $url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?';       
        $arr = array(
           'appid'=>WeixinConfig::APPID,
           'grant_type'=>'refresh_token', 
           'refresh_token'=>$refresh_token,
                
        );  
        $url .= http_build_query($arr); 
        $json = $this->curl($url);
        $arr = json_decode($json,true);
        if(!empty($arr['openid'])){
            return $arr;
        }    
        return null;    
    }
    //»ñÈ¡ÓÃ»§ÐÅÏ¢
    //access_token ÓÃ»§µÄ access_token 
    public function getUser($access_token,$openid){
        $url = 'https://api.weixin.qq.com/sns/userinfo?';
        $arr = array(
           'access_token'=>$access_token,
           'openid'=>$openid, 
           'lang'=>'zh_CN',
        );  
        $url .= http_build_query($arr); 
        $json = $this->curl($url);  
        $arr = json_decode($json,true);
        if(!empty($arr['openid'])){
            return $arr;
        }    
        return null;
        
    }
    
    //ÓÃÄ£°å·¢ËÍÏûÏ¢
    //https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN
    /*     
    ²ÎÊý	   ÊÇ·ñ±ØÌî	ËµÃ÷
    openid	     ÊÇ	    ½ÓÊÕÕßopenid
    template_id	 ÊÇ	    Ä£°åID
    url	         ·ñ	    Ä£°åÌø×ªÁ´½Ó
    data	     ÊÇ	    Ä£°åÊý¾Ý 
    */
    public function templateSend($openid,$template_id,$orderurl,$data){
        $access_token = $this->getAccessToken();
        if(!$access_token){
            return false;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $arr = array(
           'touser'=>$openid,
           'template_id'=>$template_id, 
           'url'=>$orderurl,
           'data'=>$data
        );       
        $post = json_encode($arr,JSON_UNESCAPED_UNICODE); 
        $json = $this->curl($url,$post);
        $arr = json_decode($json,true);
        if(isset($arr['errcode']) && $arr['errcode'] ==0 ){
            return true;
        }    
        return false;
    }
    //$data ²Ëµ¥Êý×é
    public function sendMenu($data){
        $access_token = $this->getAccessToken();
        if(!$access_token){
            return false;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
             
        $post = json_encode($data,JSON_UNESCAPED_UNICODE);       
        $json = $this->curl($url,$post);   
        $arr = json_decode($json,true);
        if(isset($arr['errcode']) && $arr['errcode'] ==0 ){
            return true;
        }    
        return false;
    }
    
    //»ñÈ¡JSAPIÊÚÈ¨Æ¾Ö¤
   // https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi
     //»ñÈ¡È«¾Öaccess_token
    public function getJsapiTicket($access_token=''){
        if(!$access_token){
            $access_token = $this->getAccessToken();
         }
        $jsapi_ticket = RD()->get('jsapi_ticket');
        if(!$jsapi_ticket){
            $url  = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";              
            $json = $this->curl($url);
            $arr = json_decode($json,true);          
            $jsapi_ticket = $arr['ticket'] ?? null;        
            $expires_in = $arr['expires_in'] ?? 0;
            if( $jsapi_ticket && $expires_in){  
                RD()->set('jsapi_ticket',$jsapi_ticket,$expires_in - 300);
            }
        }        
        return $jsapi_ticket;
    }
    //»ñÈ¡JSAPIµÄconfig
    public  function getJsapiConfig($selfurl='',$nonce_str=''){
        $ticket = $this->getJsapiTicket();
        if(!$ticket) return null;
        if(!$selfurl){
            $selfurl =  'http://'.$_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            $selfurl = explode('#',$selfurl);
            $selfurl = $selfurl[0];
        } 
        if(!$nonce_str){
            $nonce_str = $this->getNonceStr();
        }        
        $url = array(
           'noncestr'=>$nonce_str, 
           'jsapi_ticket'=>$ticket,
           'timestamp'=>time(),               
           'url'=> $selfurl,   
        ); 
       //Ç©ÃûËã·¨        
        ksort($url);//×ÖµäÅÅÐò
        $buff = "";
		foreach ($url as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
       $buff = trim($buff, "&");         
       $url['signature'] = sha1($buff);
       $url['appid']  = WeixinConfig::APPID;
       return $url;
   }
   
    //´´½¨Ëæ»ú×Ö·û´®
    public static function getNonceStr($length = 8)
    {
        // ×Ö·û¼¯£¬¿ÉÈÎÒâÌí¼ÓÄãÐèÒªµÄ×Ö·û    
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        return substr(str_shuffle($chars),0,$length);
    }
    //´ò¿ªÁ¬½Ó
    function curl($url,$post = false){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//Õâ¸öÊÇÖØµã¡£
        if($post){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}