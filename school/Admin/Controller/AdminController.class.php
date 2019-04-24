<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends CommonController {

	//负责人列表页
    public function index(){
        $posid = session('posid');
        if($posid != 1){
            $this->error('您无权查看此功能!',U('index/index'));
        }
        $where['status'] = 0;
    	$admin = D("admin"); // 实例化对象
        $page = setpage('admin',$where,5);
        //分页跳转的时候保证查询条件
        if($_GET){
            $admin_name = I('get.admin_name','','trim');
            if($admin_name){
                $where['admin_name'] = array('like','%'.$admin_name.'%');
            }
            $admin_phone = I('get.admin_phone',0,'_int');

            if($admin_phone){
                $where['admin_phone'] = array('like','%'.$admin_phone.'%');
            }
            // dump($where);die;
            $page = setpage('admin',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                // if($key != 'spcount' && $key != 'asc'){
                //     $where_str .= "$key=".urlencode($val).'&';
                // }
            }
            
        }
        $list = $admin->getList($page,$where);
        $this->assign('list',$list);
        $this->assign('page',$page->show());
        $this->assign('open_admin','nav-active');
        $this->display();
    }

    //增加负责人
    public function addAdmin(){
        $posid = session('posid');
        if($posid != 1){
            $this->error('您无权查看此功能!',U('index/index'));
        }
        $admin = D('admin');
        $list = $admin->getPos();
        // dump($list);die;
        if($_POST){
            // dump($_POST);die;
            $data['admin_name'] = I('post.admin_name','','htmltotxt');
            $data['admin_name'] || $this->error('管理员名不能为空');
            $data['admin_phone'] = I('post.admin_phone',0,'_int');
            $data['admin_phone'] > 0 || $this->error('手机号不能为空');
            $res = $admin->getAdmin(['admin_phone'=>$admin_phone]);
            if($res){
                $this->error('此手机号已注册');
            }
            $data['posid'] = I('post.posid',0,'_int');
            $data['posid'] > 0 || $this->error('请选择管理员类型');
            $data['pass_name'] = I('post.pass_name','','htmltotxt');
            $data['pass_name'] || $this->error('登录名不能为空');
            $data['password'] = I('post.password','','htmltotxt');
            $data['password']|| $this->error('密码不能为空');            
            $data['repassword'] = I('post.repassword','','htmltotxt');
            $data['repassword']|| $this->error('密码不能为空');
            
            // dump($data);die;
            if (!$admin->create($data)){ 
                $this->error($admin->getError(),U('admin/addAdmin'));    
            }else{
                $data['password'] = MD5($data['password']);
                $res = $admin->addAdmin($data);
                if($res){
                    $this->success('添加成功',U('admin/index'));die;
                }else{
                    $this->error('添加失败');
                }
            }
        }
        $this->assign('list',$list);
    	$this->assign('open_admin','nav-active');
    	$this->display();
    }
    //修改管理员信息
    public function editAdmin(){
        $posid = session('posid');
        if($posid != 1){
            $this->error('您无权查看此功能!',U('index/index'));
        }
        $admin = D('admin');
        $list = $admin->getPos();
        if($_GET){
            $admin_id = I('get.admin_id',0,'_int');
            $admin_id > 0 || $this->error('选择出错');
            $info = $admin->getAdmin(['admin_id'=>$admin_id]);
        }
        if($_POST){
            $admin_id = I('post.admin_id',0,'_int');
            $admin_id > 0 || $this->error('选择有误');
            $data['admin_name'] = I('post.admin_name','','htmltotxt');
            $data['admin_name'] || $this->error('管理员名不能为空');
            $map1['admin_name'] = array('EQ',$data['admin_name']);
            $map1['admin_id'] = array('NEQ',$admin_id);
            $res = $admin->getAdmin($map1);
            if($res){
                $this->error('此管理员名已注册');
            }
            $data['admin_phone'] = I('post.admin_phone',0,'_int');
            $data['admin_phone'] > 0 || $this->error('手机号不能为空');
            $map3['admin_phone'] = array('EQ',$data['admin_phone']);
            $map3['admin_id'] = array('NEQ',$admin_id);
            $res = $admin->getAdmin($map3);
            if($res){
                $this->error('此手机号已注册');
            }
            $data['posid'] = I('post.posid',0,'_int');
            $data['posid'] > 0 || $this->error('请选择管理员类型');
            $data['pass_name'] = I('post.pass_name','','htmltotxt');
            $data['pass_name'] || $this->error('登录名不能为空');
            $map2['pass_name'] = array('EQ',$data['pass_name']);
            $map2['admin_id'] = array('NEQ',$admin_id);
            $res = $admin->getAdmin($map2);
            if($res){
                $this->error('此登录名已注册');
            }
            if (!$admin->create($data,2)){ 
                $this->error($admin->getError(),U('admin/editAdmin',['admin_id'=>$admin_id]));    
            }else{
                $res = $admin->editAdmin(['admin_id'=>$admin_id],$data);
                if($res){
                    $this->success('修改成功',U('admin/index'));die;
                }else{
                    $this->error('修改失败');
                }
            }
        }
        $this->assign('info',$info);
        $this->assign('list',$list);
        $this->assign('open_admin','nav-active');
        $this->display();

    }
    //删除信息
    public function delAdmin(){
        $posid = session('posid');
        if($posid != 1){
            $this->error('您无权查看此功能!',U('index/index'));
        }
        $admin_id = I('post.id',0,'_int');
        $admin_id > 0 || $this->error('选择有误，请重新选取');
        $admin = D('admin');
        $res = $admin->delAdmin(['admin_id'=>$admin_id]);
        if($res){
            echo 1;//删除成功
        }else{
            echo 0;//删除失败
        }
    }
    //查看登录日志
    public function loginLog(){
        $posid = session('posid');
        if($posid != 1){
            $this->error('您无权查看此功能!',U('index/index'));
        }
        $page = setpage('login_log',$where,5);
        $list = M('login_log as l')
                ->field('l.*,a.admin_name,a.pass_name')
                ->join('admin as a on a.admin_id = l.admin_id')
                ->select();
        $this->assign('list',$list);
        $this->assign('page',$page->show());
        $this->display();
    }
}