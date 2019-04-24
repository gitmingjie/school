<?php
namespace Admin\Controller;
use Think\Controller;
class EquipController extends CommonController {

	//仓库列表页
    public function index(){
        $equip = D("equip"); // 实例化对象
        $where = [];
        $page = setpage('equip',$where,5);
        //分页跳转的时候保证查询条件
        if($_GET){
            $zname = I('get.zname','','trim');
            if($zname){
                $where['zname'] = array('like','%'.$zname.'%');
            }
            $equip_name = I('get.equip_name','','trim');
            if($equip_name){
                $where['equip_name'] = array('like','%'.$equip_name.'%');
            }
            // dump($where);die;
            $page = setpage('equip',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                // if($key != 'znum' && $key != 'asc'){
                //     $where_str .= "$key=".urlencode($val).'&';
                // }
            }
            
        }
        $list = $equip->getList($page,$where);
          // dump($list);die;
        $this->assign('status',['关闭','开启']);
        $this->assign('list',$list);
        // $this->assign('where_str',$where_str);
        $this->assign('page',$page->show());
        $this->assign('open_equip','nav-active');
        $this->display();
    }

    //增加设备
    public function addEquip(){
        $ware = D('ware');
        // dump($ware);die;
        $list = $ware->getAll();
        if($_POST){
            // dump($_POST);die;
            $data['equip_name'] = I('post.equip_name','','htmltotxt');
            $data['equip_name'] || $this->error('设备名不能为空');
            $data['zid'] = I('post.zid',0,'_int');
            $data['zid'] > 0 || $this->error('请选择分区');
            $data['bind'] = I('post.bind','','htmltotxt');
            $data['bind'] || $this->error('类型不能为空');
            $equip = D('equip');
            // dump($equip);die;
            if (!$equip->create($data)){ 
                $this->error($equip->getError(),U('equip/addEquip'));    
            }else{

                $res = $equip->addEquip($data);
                if($res){
                    $this->success('添加新设备成功',U('equip/index'));die;
                }else{
                    $this->error('添加失败');
                }
            }
        }
        $this->assign('list',$list);
    	$this->assign('open_equip','nav-active');
    	$this->display();
    }

    //ajax选择分区
    public function ajaxWare(){
        $wid = I('post.wid',0,'_int');
        if($wid > 0){
            $zone = D('zone');
            $list = $zone->getAll(['wid'=>$wid]);
            if($list){
                echo json_encode($list);
            }
        }
    }
    //修改设备信息
    public function editEquip(){
        $equip = D('equip');
        if($_GET){
            $equip_id = I('get.equip_id',0,'_int');
            $equip_id > 0 || $this->error('选择有误');
            $info = $equip->getEquip(['$equip_id'=>$equip_id]);
        }
        if($_POST){
            // dump($_POST);die;
            $equip_id = I('post.equip_id',0,'_int');
            $equip_id > 0 || $this->error('选择有误');
            $data['equip_name'] = I('post.equip_name','','htmltotxt');
            $data['equip_name'] || $this->error('设备名不能为空');
            if (!$equip->create($data,2)){ 
                $this->error($equip->getError(),U('equip/addEquip'));    
            }else{

                $res = $equip->editEquip(['equip_id'=>$equip_id],$data);
                if($res){
                    $this->success('修改新设备成功',U('equip/index'));die;
                }else{
                    $this->error('修改失败');
                }
            }
        }
        $this->assign('info',$info);
        $this->assign('open_equip','nav-active');
        $this->display();
    }

    //删除设备信息
    public function delEquip(){
        $equip_id = I('post.id',0,'_int');
        if($equip_id > 0){
            $equip = D('equip');
            $res = $equip->delEquip(['equip_id'=>$equip_id]);
            if($res){
                echo 1;
            }else{
                echo 0;
            }
        }
    }

    //设备启动记录
    public function logIndex(){
        $equip_id = I('get.equip_id',0,'_int');
        $where = [];
        if($equip_id > 0){
            $where1['equip_id'] = I('get.equip_id',0,'_int');
            $where['o.equip_id'] = I('get.equip_id',0,'_int');
        }
        $equip = D("equip"); // 实例化对象
        $page = setpage('operate_log',$where1,5);
        //分页跳转的时候保证查询条件
        $operate = 2;
        if($_GET){
            $operate = I('get.operate',2,'trim');
            if($operate < 2){
                $where['operate'] = $operate;
                $where1['operate'] = $operate;
            }
            // dump($where);die;
            $page = setpage('equip',$where1,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                // if($key != 'znum' && $key != 'asc'){
                //     $where_str .= "$key=".urlencode($val).'&';
                // }
            }
            
        }
        $list = $equip->getLogList($page,$where);
           // dump($list);die;
        $this->assign('operate',$operate);
        $this->assign('equip_id',$equip_id);
        $this->assign('status',['关闭','开启']);
        $this->assign('list',$list);
        // $this->assign('where_str',$where_str);
        $this->assign('page',$page->show());
        $this->assign('open_equip','nav-active');
        $this->display();
    }
   
}