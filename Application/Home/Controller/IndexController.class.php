<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	 /**
     * 首页
     * @access public
     * @param callable $callable
     * @return void
     * @author jin 2019/04/20 18:08
     */
    public function index(){
       $this->display();
    }
    /**
     * 头文件
     * @access public
     * @param callable $callable
     * @return void
     * @author jin 2019/04/20 18:08
     */
    public function public_html(){
       $this->display('Public/Public');
    }
    public function test(){
        
        $a = [
            'code'=>1,
            'msg'=>'登录成功'
        ];
        echo json_encode($a);die;
    }
}