<?php
namespace Admin\Controller;
header('content-type:text/html;charset=utf-8');
use Think\Controller;
class CommonController extends Controller {
    public function _initialize(){
        // 自动运行方法
        if(!session("?adminid")){
            $this->error("请先登录",U('login/index'));
        }
    
    }
}