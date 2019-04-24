<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {

	public function index(){
        $this->display();
    }
    //登陆
    public function login(){
        // dump($_POST);die;
        $mod = M('admin');
        $pass_name = I('post.pass_name','','htmltotxt');
        $pass_name || $this->error('请填写登录名');
        $password = I('post.password','','htmltotxt');
        $password || $this->error('请填写登录密码');
        $where['pass_name'] = $pass_name;
        $where['status'] = 0;
           //var_dump($where);die;
        $info = $mod->where($where)->find();
        if($info){
            if(MD5($password) == $info['password']){
                session('adminid',$info['admin_id']);
                session('posid',$info['posid']);
                //写入登录日志
                $data = array(
                    'admin_id'=>$info['admin_id'],
                    'last_time'=>time(),
                    'last_ip'=>$_SERVER["REMOTE_ADDR"]
                    );
                M('login_log')->add($data);
                $this->success('登录成功',U('Index/index'));
            }else{
                $this->error('登录失败,用户密码错误');
            }
        }else{
            $this->error('登录失败,用户不存在');
        }
    }
    //退出登陆
    public function outLogin(){
        session(null);
        $this->success('退出成功',U('login/index'));
    }
    
}