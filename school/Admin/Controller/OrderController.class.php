<?php
namespace Admin\Controller;
use Think\Controller;
class OrderController extends CommonController {

	//订单列表页
    public function index(){
    	$order = D('orders');
    	$where = [];
    	$where1 = [];
        $page = setpage('orders',$where,5);
        //分页跳转的时候保证查询条件
        if($_GET){
            $sname = I('get.sname','','trim');
            if($sname){
                $where['sname'] = array('like','%'.$sname.'%');
                $where1['c.sname'] = array('like','%'.$sname.'%');
            }
            $contact = I('get.contact',0,'_int');
            if($contact){
                $where['contact'] = array('like','%'.$contact.'%');
                $where1['contact'] = array('like','%'.$contact.'%');
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
              $where['out_time'] = array('between',array($start_time,$end_time));
          }
            // dump($where);die;
            $page = setpage('orders',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                if($key != 'spcount' && $key != 'asc'){
                    $where_str .= "$key=".urlencode($val).'&';
                }
            }
            
        }
        $list = $order->getList($page,$where);
        // dump($list);die;
        $this->assign('where_str',$where_str);
        $this->assign('list',$list);
        $this->assign('status',['正常','赠品']);
        $this->assign('page',$page->show());	
        $this->assign('open_order','nav-active');
        $this->display();
    }
    //新订单
    public function newOrder(){
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
        $this->assign('open_order','nav-active');
        $this->display();
    }
    //订购
    public function orderGoods(){
    	if($_GET){
    		 // dump($_GET);
    		if(session('?gid')){
    			$data = session('gid');
    			$new = [];
    			foreach($data as $k=>$v){
    				if($_GET['gid'] != $k){
    					$data[$_GET['gid']] = 1;
    				}
    			}
    		}else{
    			$data[$_GET['gid']] = 1;
    		}
    		session('gid',$data);
    		echo '添加货物成功';
	        echo '<br/><a href="http://school.com/Admin/Order/newOrder" title="">点我继续购物</a>';
	        echo '<br/> 3S以后跳转到订单页面';
	        echo '<meta http-equiv="refresh" content="3;url=http://school.com/Admin/Order/addOrder">';
    	}
    }
    //下一步
    public function addOrder(){
    	$customer = D('customer');
    	$list = $customer->getAll();
    	$order = D('orders');
    	if($_POST){
    		// dump($_POST);die;
    		if (!$order->create($_POST)){ 
                $this->error($order->getError(),U('order/addOrder'));    
            }else{
                $res = $order->addOrder($_POST);
                if($res){
                	//添加成功
                	$gids = session('gid');
                	$all_price = 0;
                	foreach($gids as $k=>$v){
                		$info = D('goods')->getGoods(['gid'=>$k]);
                		if($info){
                			$data['order_id'] = $res;
                			$data['spid'] = $info['spid'];
                			$data['zid'] = $info['zid'];
                			$data['gid'] = $k;
                			$data['num'] = $v;
                			$data['status'] = 0;
                			$data['price'] = $v*$info['in_price'];
                			$all_price += $v*$info['in_price'];
                			M('order_goods')->add($data);
                			D('goods')->downGoods(['gid'=>$k],$v);
                		}
                	}
                	session('gid',null); // 删除gid
                	// dump($res);die;
                	D('orders')->editOrder(['order_id'=>$res],['all_price'=>$all_price]);
                    //下单数+1
                    M('customer')->where(['sid'=>$_POST['sid']])->setInc('scount',1);
                    $this->success('添加成功',U('order/index'),2);die;
                }else{
                    $this->error('添加失败');
                }
            }
    	}
    	$this->assign('list',$list);
        $this->assign('open_order','nav-active');
        $this->display();
    }

    //订单详情表
    public function orderGoodsInfo(){
    	$order_id = I('get.order_id',0,'_int');
    	$order_id > 0 || $this->error('选择有误');
    	$order = D('orders');
    	$where = ['order_id'=>$order_id];
        $page = setpage('order_goods',$where,5);
        //分页跳转的时候保证查询条件
        $list = $order->orderGoods($page,$where);
        // dump($list);die;
        $this->assign('list',$list);
        $this->assign('status',['未发货','已发货']);
        $this->assign('page',$page->show());	
        $this->assign('open_order','nav-active');
        $this->display();
    }
    //发货
    public function truck(){
    	$id = I('post.id',0,'_int');
    	if($id > 0){
    		$res = M('order_goods')->where(['order_goods_id'=>$id])->save(['status'=>1]);
    		if($res){
    			echo 1;
    		}else{
    			echo 0;
    		}
    	}
    }
    
}