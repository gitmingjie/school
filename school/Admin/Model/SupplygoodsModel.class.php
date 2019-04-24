<?php 

namespace Admin\Model;
use Think\Model;

class SupplyGoodsModel extends Model
{	
	//定义验证规则
	 protected $_validate = array(     
	 		array('gname','require','货物名不能为空',0,'',1), //默认情况下用正则进行验证
	 		array('code','require','货物编码不能为空',0,'',1), //默认情况下用正则进行验证
            array('in_num','/^[1-9]{1}\d{0,9}$/','供应数量不符合要求',0,'',1), //默认情况下用正则进行验证
            array('in_price','/(^[1-9]\d*(\.\d{1,2})?$)|(^0(\.\d{1,2})?$)/','供应价格不符合要求',0,'',1), //默认情况下用正则进行验证
	 		array('area','/(^[1-9]\d*(\.\d{1,2})?$)|(^0(\.\d{1,2})?$)/','规格不符合要求',0,'',1), //默认情况下用正则进行验证
            array('status','require','货物类型不能为空',0,'',1), //默认情况下用正则进行验证
            array('spid','require','请选择供应商',0,'require',1),
            array('zid','require','请选择仓库分区',0,'require',1),
	 	);
	//供应商供货
     public function supplyGoods($data){
        $data['in_sn']=date('YmdHis').mt_rand(10000000,99999999);
        $data['admin_id']=session('adminid');
        $data['in_time']=time();
        $data['is_pass']=0;
        return $this->add($data);     
    }
    //待审核列表
    public function getList($page,$where,$order="in_id",$keyword="desc"){
        return M('supplygoods as s')->field('s.*,z.zname,a.admin_name,p.spname')
                    ->join('zone as z on z.zid = s.zid')
                    ->join('supply as p on p.spid = s.spid')
                    ->join('admin as a on a.admin_id = s.admin_id')
                    ->where($where)
                    ->order(array($order=>$keyword))
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
    }
    //获取单条数据
    public function getInfo($where){
        return $this->where($where)->find();
    }
    //改变审核状态
    public function editPass($where,$data){
        return $this->where($where)->save($data);
    }
    //记录
    public function addLog($in_id,$is_pass){
        $data = array(
            'in_id'=>$in_id,
            'admin_id'=>session('adminid'),
            'pass_status'=>$is_pass,
            'pass_time'=>time()
            );
        return M('pass_log')->add($data);   
    }
    //记录列表
    public function logList($page,$where,$order="pass_id",$keyword="desc"){
        return M('pass_log as p')->field('p.*,s.code,s.gname,a.admin_name')
                    ->join('supplygoods as s on s.in_id = p.in_id')
                    ->join('admin as a on a.admin_id = p.admin_id')
                    ->where($where)
                    ->order(array($order=>$keyword))
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
    }
}
?>