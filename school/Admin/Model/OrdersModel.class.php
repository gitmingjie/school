<?php 

namespace Admin\Model;
use Think\Model;

class OrdersModel extends Model
{	
	//定义验证规则
	 protected $_validate = array(     
            array('contact','require','管理员名不能为空或已使用',0,'',1), //默认情况下用正则进行验证
	 		array('order_address','require','管理员名不能为空或已使用',0,'',1), //默认情况下用正则进行验证
	 		array('order_phone','/^(1[34578]{1}\d{9})|(\d{3,4}-\d{7,8})$/','联系方式有误',0,'',3), //默认情况下用正则进行验证
	 		
	 	);
     //增加订单
     public function addOrder($data){
        $data['order_sn'] = date('YmdHis').mt_rand(10000000,99999999);
        $data['admin_id'] = session('adminid');
        $data['all_price'] = 0;
        $data['out_time'] = time();
        return $this->add($data);
     }
     //订单列表
    public function getList($page,$where,$order="order_id",$keyword="desc"){
        
        return M('orders as r')->field('r.*,c.sname,a.admin_name')
                    ->join('customer as c on c.sid = r.sid')
                    ->join('admin as a on a.admin_id = r.admin_id')
                    ->where($where)
                    ->order(array($order=>$keyword))
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
    }
	//修改订单
    public function editOrder($where,$data){
        return $this->where($where)->save($data);
    }
    //订单详情列表怕
    public function orderGoods($page,$where,$order="order_goods_id",$keyword="desc"){
        return M('order_goods as r')->field('r.*,s.spname')
                    ->join('supply as s on s.spid = r.spid')
                    ->where($where)
                    ->order(array($order=>$keyword))
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
    }
}
?>