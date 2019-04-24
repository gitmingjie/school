<?php
namespace Admin\Controller;
use Think\Controller;
class CateController extends CommonController {

	//分类列表页
    public function index(){

    	$cate = D("Cate"); // 实例化对象
        $where = [];
        $page = setpage('cate',$where,5);
        //分页跳转的时候保证查询条件
        if($_GET){
            $cname = I('get.cname','','trim');
            if($cname){
                $where['cname'] = array('like','%'.$cname.'%');
            }
            // dump($where);die;
            $page = setpage('cate',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
            }
            
        }
        $list = $cate->getList($page,$where);
        // dump($list);die;
        $this->assign('list',$list);
        $this->assign('page',$page->show());
        $this->assign('open_cate','nav-active');
        $this->display();
    }

    //增加分类
    public function addCate(){
        $cate = D("Cate"); // 实例化对象
        $allCate = $cate->allCate();
        // dump($allCate);die;
        if($_POST){
            // dump($_POST);die;
            $data['cname'] = I('post.cname','','htmltotxt');
            $data['desc'] = I('post.desc','','htmltotxt');
            $data['pid'] = I('post.pid',0,'_int');
            if (!$cate->create($data)){ 
                $this->error($cate->getError(),U('cate/addCate'));    
            }else{
                $info = $cate->getCate(['cid'=>$data['pid']]);
                $data['pids'] = $info['pids'].$data['pid'].','; 
                $res = $cate->addcate($data);
                if($res){
                    $this->success('添加成功',U('cate/index'),3);die;
                }else{
                    $this->error('添加失败');
                }
            }
        }
        $this->assign('allCate',$allCate);
    	$this->assign('open_cate','nav-active');
    	$this->display();
    }
    //添加子集
    public function addChild(){
        $cate = D("Cate"); // 实例化对象
        if($_GET){
             $cid = I('get.cid',0,'_int');
            $cid > 0 || $this->error('选择有误，请重新选择');
            $info = $cate->getCate(['cid'=>$cid]);
        }
       
        // dump($allCate);die;
        if($_POST){
             // dump($_POST);die;
            $data['cname'] = I('post.cname','','htmltotxt');
            $data['desc'] = I('post.desc','','htmltotxt');
            $data['pid'] = I('post.pid',0,'_int');
            if (!$cate->create($data)){ 
                $this->error($cate->getError(),U('cate/addChild','cid='.$data['pid']));    
            }else{
                $info = $cate->getCate(['cid'=>$data['pid']]);
                $data['pids'] = $info['pids'].$data['pid'].','; 
                $res = $cate->addcate($data);
                if($res){
                    $this->success('添加成功',U('cate/index'));die;
                }else{
                    $this->error('添加失败');
                }
            }
        }
        $this->assign('info',$info);
        $this->assign('open_cate','nav-active');
        $this->display();
    }

    //修改分类
    public function editCate(){
       $cate = D("Cate"); // 实例化对象
       if($_GET){
        $cid = I('get.cid',0,'_int');
        $cid > 0 || $this->error('选择有误，请重新选择');
        $info = $cate->getCate(['cid'=>$cid]);
       }
        if($_POST){
            // dump($_POST);die;
            $data['desc'] = I('post.desc','','htmltotxt');
            $data['cname'] = I('post.cname','','htmltotxt');
            $cid = I('post.cid',0,'_int');
            $data['desc'] || $this->error('描述不能为空');
            $cid > 0 || $this->error('选择有误，请重新选择');
            $data['cname'] || $this->error('分类名不能为空');
            $res = $cate->editcate(['cid'=>$cid],$data);
            if($res){
                $this->success('修改成功',U('cate/index'),3);die;
            }else{
                $this->error('修改失败');
            }
        }
        $this->assign('info',$info);
        $this->assign('open_cate','nav-active');
        $this->display(); 
    }

    //删除分类信息
    public function delCate(){
        // dump($_POST);die;
        $cid = I('post.cid',0,'_int');
        $cid > 0 || $this->error('选择有误，请重新选取');
        $cate = D('Cate');
        //判定是否有子级
        $child = $cate->getCate(['pid'=>$cid]);
        if($child){
            echo 2;
            die;
        }
        $res = $cate->delCate(['cid'=>$cid]);
        if($res){
            echo 1;//删除成功
        }else{
            echo 0;//删除失败
        }
    }
}