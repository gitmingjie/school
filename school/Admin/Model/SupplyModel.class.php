<?php 

namespace Admin\Model;
use Think\Model;

class SupplyModel extends Model
{	
	//定义验证规则
	 protected $_validate = array(     
	 		array('spname','require','公司名字为空或已注册',0,'unique',1), //默认情况下用正则进行验证
	 		array('spemail','email','邮箱格式有误或已注册',0,'unique',1), //默认情况下用正则进行验证
	 		array('spon','require','请输入联系人',0,'',1), //默认情况下用正则进行验证
	 		array('spphone','/^(1[34578]{1}\d{9})|(\d{3,4}-\d{7,8})$/','联系方式有误或已注册',0,'unique',1), //默认情况下用正则进行验证
	 		array('spaddress','require','请输入公司所在地',0,'',1), //默认情况下用正则进行验证
	 	);
	//添加供应商
    public function addSupply($data){
    	return $this->add($data);
    }

    //供应商列表
    public function getList($page,$where,$order="spid",$keyword="desc"){
        $where['status'] = 0;
    	return $this->where($where)->order(array($order=>$keyword))->limit($page->firstRow,$page->listRows)->select();
    }
    //查询单个客户
    public function getSupply($where){
    	return $this->where($where)->find();
    }

    //修改客户信息
    public function eidtSupply($where,$data){
        return $this->where($where)->save($data);
    }
    //获取所有供应商信息
    public function getAll($where=[]){
        return $this->where($where)->select();
    }
}
?>