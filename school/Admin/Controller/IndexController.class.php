<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
        $admin_info = M('admin as a')->field('a.*,p.upos')->join('pos as p on p.posid = a.posid')->where(['admin_id'=>session('adminid')])->find();
        $login_log = M('login_log')->where(['admin_id'=>session('adminid')])->select();
        $end = end($login_log);
        $this->assign('end',$end);
        $this->assign('serve',$_SERVER);
        // dump($_SERVER);die;
        $this->assign('admin_info',$admin_info);
        $this->assign('pos_info',$pos_info);
        $this->display();
    }
}