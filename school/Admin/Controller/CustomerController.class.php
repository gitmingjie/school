<?php
namespace Admin\Controller;
use Think\Controller;
class CustomerController extends CommonController {

	//供应商列表页
    public function index(){
        $customer = D("Customer"); // 实例化对象
        $where['status'] = 0;
        $order = isset($_GET['order'])?$_GET['order']:'sid';
        $desc = isset($_GET['asc'])?$_GET['asc']:'desc';
        $page = setpage('customer',$where,5);
        //分页跳转的时候保证查询条件
        if($_GET){
            $sname = I('get.sname','','trim');
            if($sname){
                $where['sname'] = array('like','%'.$sname.'%');;
            }
            $sphone = I('get.sphone',0,'_int');
            if($sphone){
                $where['sphone'] = $sphone;
            }
            $page = setpage('customer',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                if($key != 'spcount' && $key != 'asc'){
                    $where_str .= "$key=".urlencode($val).'&';
                }
            }
            
        }
        $list = $customer->getList($page,$where,$order,$desc);
        $this->assign('where_str',$where_str);
        $this->assign('list',$list);
        $this->assign('page',$page->show());
        $this->assign('open_customer','nav-active');
        $this->display();
    }

    //增加供应商
    public function addCustomer(){
        if($_POST){
             // dump($_POST);
            $data['sname'] = I('post.sname','','htmltotxt,trim');
            $data['semail'] = I('post.semail','','htmltotxt');
            $data['scon'] = I('post.scon','','htmltotxt,trim');
            $data['sphone'] = I('post.sphone',0,'_int');
            $data['saddress'] = I('post.saddress','','htmltotxt');
            $data['snote'] = I('post.snote','','htmltotxt');
            // dump($data);die;
            $customer = D("customer"); // 实例化对象
            if (!$customer->create($data)){ 
                $this->error($customer->getError(),U('Customer/addcustomer'));    
            }else{
                $res = $customer->addCustomer($data);
                if($res){
                    $this->success('添加成功',U('Customer/index'));die;
                }else{
                    $this->error('添加失败');
                }
            }
        }
    	$this->assign('open_customer','nav-active');
    	$this->display();
    }
    //修改客户
    public function editCustomer(){
        if($_GET){
            $sid = I('get.sid',0,'_int');
            $sid > 0 || $this->error('选择有误');
        }
        $customer = D("customer"); // 实例化对象
        $list = $customer->getCustomer(['sid'=>$sid]);
        if($_POST){
            // $data['sname'] = I('post.sname','','htmltotxt,trim');
            // $data['semail'] = I('post.semail','','htmltotxt');
            $data['scon'] = I('post.scon','','htmltotxt,trim');
            $data['sphone'] = I('post.sphone',0,'_int');
            $data['saddress'] = I('post.saddress','','htmltotxt');
            $data['snote'] = I('post.snote','','htmltotxt');
            $sid = I('post.sid',0,'_int');
            $sid > 0 || $this->error('选择有误1');
            if (!$customer->create($data,2)){ 
                $this->error($customer->getError(),U('Customer/editCustomer',['sid'=>$sid]));    
            }else{
                $res = $customer->eidtCustomer(['sid'=>$sid],$data);
                if($res){
                    $this->success('修改成功',U('Customer/index'));die;
                }else{
                    $this->error('修改失败');
                }
            }
        } 
        // dump($list);die;
        $this->assign('list',$list);
        $this->assign('open_customer','nav-active');
        $this->display();
    }

    //删除客户信息
    public function delCustomer(){
        // dump($_POST);die;
        $sid = I('post.sid',0,'_int');
        $sid > 0 || $this->error('选择有误，请重新选取');
        $customer = D('Customer');
        $res = $customer->eidtCustomer(['sid'=>$sid],['status'=>1]);
        if($res){
            echo 1;//删除成功
        }else{
            echo 0;//删除失败
        }
    }
}