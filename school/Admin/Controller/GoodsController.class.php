<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extends CommonController {

	//货物列表页
    public function index(){
        $goods = D("goods"); // 实例化对象
        $where['goods_status'] = 0;
        $order = isset($_GET['order'])?$_GET['order']:'gid';
        $desc = isset($_GET['asc'])?$_GET['asc']:'desc';
        $page = setpage('goods',$where,5);
        $operate = 2;
        //分页跳转的时候保证查询条件
        if($_GET){
            $gname = I('get.gname','','trim');
            if($gname){
                $where['gname'] = array('like','%'.$gname.'%');
            }
            $code = I('get.code','','trim');
            if($code){
                $where['code'] = array('like','%'.$code.'%');
            }
            $status = I('get.status',0,'_int');
            if($status > 0){
                $where['status'] = $status;
                $operate = $status;
            }
            // dump($where);die;
            $page = setpage('goods',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                if($key != 'spcount' && $key != 'asc'){
                    $where_str .= "$key=".urlencode($val).'&';
                }
            }
            
        }
        // dump($supplygoods);die;
        $list = $goods->getList($page,$where,$order,$desc);
        $this->assign('where_str',$where_str);
        $this->assign('list',$list);
        $this->assign('operate',$operate);
        $this->assign('status',['正常','退货']);
        $this->assign('page',$page->show());
        $this->assign('open_goods','nav-active');
        $this->display();
    }

    //商户供货
    public function supplyGoods(){
        $supplygoods = D('supplygoods');
        $supply = D('supply');
        $ware = D('ware');
        $ware_list = $ware->getAll();
        $supply_list = $supply->getAll();
         
        if($_POST){
             // dump($_POST);die;
            if (!$supplygoods->create($_POST)){ 
                $this->error($supplygoods->getError(),U('goods/supplyGoods'));    
            }else{
                $res = $supplygoods->supplyGoods($_POST);
                // dump($res);die;
                if($res){
                    $this->success('添加成功',U('inware/addGoods'));die;
                }else{
                    $this->error('添加失败');
                }
            }
        }
        $this->assign('supply_list',$supply_list);
        $this->assign('ware_list',$ware_list);
    	$this->assign('open_goods','nav-active');
    	$this->display();
    }

    //修改信息
    public function editGoods(){
        $goods = D('goods');
        if($_GET){
            $gid = I('get.gid',0,'_int');
            $gid > 0 || $this->error('选择有误');   
            $info = $goods->getGoods(['gid'=>$gid]);
        }

        if($_POST){
             // dump($_POST);die;
            $gid = I('post.gid',0,'_int');
            $gid > 0 || $this->error('选择有误');
            if (!$goods->create($_POST)){ 
                $this->error($goods->getError(),U('goods/editGoods',['gid'=>$gid]));    
            }else{
                $res = $goods->editGoods(['gid'=>$gid],$_POST);
                // dump($res);die;
                if($res){
                    $this->success('修改成功',U('goods/index'));die;
                }else{
                    $this->error('修改失败');
                }
            }
        }
        $this->assign('info',$info);
        $this->assign('open_goods','nav-active');
        $this->display();
        
    }
    //下架
    public function del(){
        $goods = D('goods');
        $gid = I('post.id',0,'_int');
        if($gid > 0){
            $res = $goods->editGoods(['gid'=>$gid],['goods_status'=>1]);
            if($res){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 0;
        }
    }
}