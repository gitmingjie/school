<?php
namespace Admin\Controller;
use Think\Controller;
class InwareController extends CommonController {

	//入库记录列表页
    public function index(){

    	$supplygoods = D("supplygoods"); // 实例化对象
        $page = setpage('pass_log',$where,5);
        //分页跳转的时候保证查询条件
        if($_GET){
            $pass_status = I('get.pass_status',0,'_int');
            if($pass_status > 0){
                $where['pass_status'] = $pass_status;
            }
            // 时间查询
          $start_time = I('get.start_time','','htmltotxt');         
          $start_time =  $start_time ? strtotime($start_time) : 0;

            $end_time = I('get.end_time','','htmltotxt');      
          $end_time =  $end_time ? strtotime($end_time.'23:59:59') : 0;

          if( $start_time  && !$end_time ){
              $end_time = $start_time + 86400 *30;
          }

          if( $start_time  && $end_time ){
              $where['pass_time'] = array('between',array($start_time,$end_time));
          }
          // dump($end_time);
          // dump($where);die;
            $page = setpage('pass_log',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                // if($key != 'spcount' && $key != 'asc'){
                //     $where_str .= "$key=".urlencode($val).'&';
                // }
            }
            
        }
        // dump($supplygoods);die;
        $list = $supplygoods->logList($page,$where);
        // $this->assign('where_str',$where_str);
        $this->assign('list',$list);
        $this->assign('pass_status',$pass_status);
        $this->assign('status',['未审核','通过','未通过']);
        $this->assign('page',$page->show());
        $this->assign('open_inware','nav-active');
        $this->display();
    }

    //货物入库
    public function addGoods(){
        $supplygoods = D("supplygoods"); // 实例化对象
        $where['is_pass'] = 0;
        $order = isset($_GET['order'])?$_GET['order']:'in_id';
        $desc = isset($_GET['asc'])?$_GET['asc']:'desc';
        $page = setpage('supplygoods',$where,5);
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
            $page = setpage('supplygoods',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                if($key != 'spcount' && $key != 'asc'){
                    $where_str .= "$key=".urlencode($val).'&';
                }
            }
            
        }
        // dump($supplygoods);die;
        $list = $supplygoods->getList($page,$where,$order,$desc);
        $this->assign('where_str',$where_str);
        $this->assign('list',$list);
        $this->assign('operate',$operate);
        $this->assign('status',['正常','退货']);
        $this->assign('page',$page->show());
    	$this->assign('open_inware','nav-active');
    	$this->display();
    }
    //审核入库
    public function access(){
        $in_id = I('post.id',0,'_int');
        if($in_id > 0){
            $supplygoods = D("supplygoods"); // 实例化对象
            $info = $supplygoods->getInfo(['in_id'=>$in_id]);
            if($info){
                if($info['is_pass'] == 0){
                    $res = $supplygoods->editPass(['in_id'=>$in_id],['is_pass'=>1]);
                    if($res){
                        //通过,记录入库记录
                        $supplygoods->addLog($in_id,1);
                        //添加至仓库
                        $goods = D('goods');
                        $ret = $goods->getGoods(['code'=>$info['code']]);
                        if($ret){
                            //仓库中还有货物
                            $goods->upGoods(['code'=>$info['code']],$info['in_num']);
                        }else{
                            //新增货物
                            $data = array(
                                'code'=>$info['code'],
                                'gname'=>$info['gname'],
                                'zid'=>$info['zid'],
                                'spid'=>$info['spid'],
                                'in_price'=>$info['in_price'],
                                'gsum'=>$info['in_num'],
                                'zarea'=>$info['area'],
                                'goods_status'=>0,
                                );
                            $goods->addGoods($data);
                        }
                        //供单数+1
                        M('supply')->where(['spid'=>$info['spid']])->setInc('spcount',1);
                        
                        // dump($res);die;
                        echo 1;
                    }else{
                        echo 0;
                    }
                }
            }  
        }
    }

    //未通过
    public function rebut(){
        $in_id = I('post.id',0,'_int');
        if($in_id > 0){
            $supplygoods = D("supplygoods"); // 实例化对象
            $info = $supplygoods->getInfo(['in_id'=>$in_id]);
            if($info){
                if($info['is_pass'] == 0){
                    $data['is_pass'] = 2;
                    $res = $supplygoods->editPass(['in_id'=>$in_id],$data);
                    if($res){
                        //未通过,记录入库记录
                        $supplygoods->addLog($in_id,2);
                        echo 1;
                    }else{
                        echo 0;
                    }
                }
            }  
        }
    }
}