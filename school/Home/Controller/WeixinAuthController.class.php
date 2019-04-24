<?php 

//微信授权登录接口
class WeixinAuthController{
    //微信登录
    function index(){  
        $id =  I('get.id',0,'htmltotxt');
        $state = I('get.state',0,'_int'); 
        $return_url = U('www@/Home/WeixinAuth/login',['id'=>$id]);
        $url = D('WeixinAuth')->getAuthUrl($return_url,'snsapi_userinfo');
       //echo  $url;
        redirect($url);
    }
    //微信登录成功回调
    /*
    { "access_token":"ACCESS_TOKEN","expires_in":7200,"refresh_token":"REFRESH_TOKEN","openid":"OPENID","scope":"SCOPE" } 
   */
    function login(){
        $code = I('get.code','','htmltotxt'); 
        $id =  I('get.id',0,'htmltotxt');
        $state = I('get.state',0,'_int'); 
        if($code){
            //获取用户信息
            $user =  D('WeixinAuth')->getOpenid($code);         
            if($user){  
               $data = array(                 
                   'access_token'=>$user['access_token'],
                   'refresh_token'=>$user['refresh_token'],
                   'expires_time'=>time() + $user['expires_in'],               
               );              
               $open_id = $user['openid'];
               $auth =   M('weixin_user')->where(array('open_id'=>$open_id))->find();             
               //存在授权
               if($auth){  
                    // $user_id = $auth['user_id'];                                         
                    // //若是订阅的，则需要获取头像                    
                    // if(!$auth['access_token']){
                    //     $this->savephoto($user_id,$user['access_token'],$user['openid']);                       
                    // }                       
                    // //获取最近的商品
                    // $shop_id = $auth['shop_id'];
                    // $shop = AuthMdl::getShopId($auth);  
                    // if( $shop){
                    //    $data['shop_id'] =  $shop_id =   $shop['shop_id'];  
                    // }                
                    // session('shop_id', $shop_id); 
                    // AuthMdl::setAuth($auth['auth_id'],$data); 
               }
               //写入授权
               else{
                   //创建会员
                   $data = [];                                       
                   $data['openid']  = $open_id;
                   $data['add_time']  = time(); 
                   $res  = $user_id = M('weixin_user')->add($data);                  
                   $this->savephoto($user_id,$user['access_token'],$user['openid']);   
               } 
            }
        }
        die('error|授权失败');
    }
    //更新头像
   private function savephoto($user_id,$access_token,$openid){
       //更新会员信息  
        $data = [];       
        $wxUser = D('WeixinAuth')->getUser($access_token,$openid); 
        if($wxUser){
            $data['nick_name'] =$wxUser['nickname'];
            $data['photo']     =$wxUser['headimgurl'];   
            M('weixin_user')->where(array('id'=>$user_id))->save( $data);             
        }    
    }
    
    //创建菜单
    function menulist(){
        $data = [
           "button"=>[
                [	
                    "type"=>"view",
                    "name"=>"点餐",
                    "url"=>U('www@/api/WeixinAuth/')
                ],
              
                [	
                    "type"=>"view",
                    "name"=>"会员",
                    "url"=>U('www@/api/WeixinAuth/',['state'=>1])
                ],
                [	
                    "type"=>"view",
                    "name"=>"订单",
                    "url"=>U('/api/WeixinAuth/',['state'=>2])
                ],
                  /*
                [	
                    "type"=>"click",
                    "name"=>"今日歌曲",
                    "key"=>"V1001_TODAY_MUSIC"
                ],
                */
                /*
                [
                    "name":"菜单",
                    "sub_button":[
                        [	
                        "type":"view",
                        "name":"搜索",
                        "url":"http://www.soso.com/"
                        ],
                        [
                        "type":"view",
                        "name":"视频",
                        "url":"http://v.qq.com/"
                        ],
                        [
                        "type":"click",
                        "name":"赞一下我们",
                        "key":"V1001_GOOD"
                        ]
                    ]
                ]
                */
            ]
        ];
        $ret = WeChat::getAuth()->sendMenu($data);
        var_dump($ret);
        
    }
}