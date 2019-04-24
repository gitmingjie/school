<?php
namespace Admin\Controller;
use Think\Controller;
class SupplyController extends CommonController {

	//供应商列表页
    public function index(){
        $supply = D("Supply"); // 实例化对象
        $where['status'] = 0;
        $order = isset($_GET['order'])?$_GET['order']:'spid';
        $desc = isset($_GET['asc'])?$_GET['asc']:'desc';
        $page = setpage('supply',$where,5);
        //分页跳转的时候保证查询条件
        if($_GET){
            $spname = I('get.spname','','trim');
            if($spname){
                $where['spname'] = array('like','%'.$spname.'%');
            }
            $spphone = I('get.spphone',0,'_int');

            if($spphone){
                $where['spphone'] = $spphone;
            }
            // dump($where);die;
            $page = setpage('supply',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                if($key != 'spcount' && $key != 'asc'){
                    $where_str .= "$key=".urlencode($val).'&';
                }
            }
            
        }
        $list = $supply->getList($page,$where,$order,$desc);
        $this->assign('where_str',$where_str);
        $this->assign('list',$list);
        $this->assign('page',$page->show());
        $this->assign('open_supply','nav-active');
        $this->display();
    }

    //增加供应商
    public function addSupply(){
        if($_POST){
            // dump($_POST);die;
            $data['spname'] = I('post.spname','','htmltotxt,trim');
            $data['spemail'] = I('post.spemail','','htmltotxt');
            $data['spon'] = I('post.spon','','htmltotxt,trim');
            $data['spphone'] = I('post.spphone',0,'_int');
            $data['spaddress'] = I('post.spaddress','','htmltotxt');
            $data['spnote'] = I('post.spnote','','htmltotxt');
            $supply = D("Supply"); // 实例化对象
            if (!$supply->create($data)){ 
                $this->error($supply->getError(),U('Supply/addSupply'));    
            }else{
                $res = $supply->addSupply($data);
                if($res){
                    $this->success('添加成功',U('Supply/index'));die;
                }else{
                    $this->error('添加失败');
                }
            }
        }
    	$this->assign('open_supply','nav-active');
    	$this->display();
    }
    //修改供应商
    public function editSupply(){
        if($_GET){
            $spid = I('get.spid',0,'_int');
            $spid > 0 || $this->error('选择有误');
        }
        $supply = D("supply"); // 实例化对象
        $list = $supply->getSupply(['spid'=>$spid]);
        if($_POST){
            // $data['sname'] = I('post.sname','','htmltotxt,trim');
            // $data['semail'] = I('post.semail','','htmltotxt');
            $data['spon'] = I('post.spon','','htmltotxt,trim');
            $data['spphone'] = I('post.spphone',0,'_int');
            $data['spaddress'] = I('post.spaddress','','htmltotxt');
            $data['spnote'] = I('post.spnote','','htmltotxt');
            $spid = I('post.spid',0,'_int');
            $spid > 0 || $this->error('选择有误1');
            if (!$supply->create($data,2)){ 
                $this->error($supply->getError(),U('Supply/editSupply',['spid'=>$spid]));    
            }else{
                $res = $supply->eidtSupply(['spid'=>$spid],$data);
                if($res){
                    $this->success('修改成功',U('supply/index'));die;
                }else{
                    $this->error('修改失败');
                }
            }
        } 
        // dump($list);die;
        $this->assign('list',$list);
        $this->assign('open_supply','nav-active');
        $this->display();
    }
    //删除供应商信息
    public function delSupply(){
        // dump($_POST);die;
        $spid = I('post.spid',0,'_int');
        $spid > 0 || $this->error('选择有误，请重新选取');
        $supply = D('Supply');
        $res = $supply->eidtSupply(['spid'=>$spid],['status'=>1]);
        if($res){
            echo 1;//删除成功
        }else{
            echo 0;//删除失败
        }
    }
}