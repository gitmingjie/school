<?php 

namespace Admin\Model;
use Think\Model;

class CustomerModel extends Model
{	
	//定义验证规则
	 protected $_validate = array(     
	 		array('sname','require','公司名字为空或已注册',0,'unique',1), //默认情况下用正则进行验证
	 		array('semail','email','邮箱格式有误或已注册',0,'unique',1), //默认情况下用正则进行验证
	 		array('scon','require','请输入联系人',0,'',3), //默认情况下用正则进行验证
	 		array('sphone','/^(1[34578]{1}\d{9})|(\d{3,4}-\d{7,8})$/','联系方式有误或已注册',0,'unique',1), //默认情况下用正则进行验证
	 		array('saddress','require','请输入公司所在地',0,'',3), //默认情况下用正则进行验证
	 	);
	//添加客户
    public function addCustomer($data){
    	return $this->add($data);
    }

    //客户列表
    public function getList($page,$where,$order="sid",$keyword="desc"){
        $where['status'] = 0;
    	return $this->where($where)->order(array($order=>$keyword))->limit($page->firstRow,$page->listRows)->select();
    }

    //查询单个客户
    public function getCustomer($where){
    	return $this->where($where)->find();
    }

    //修改客户信息
    public function eidtCustomer($where,$data){
        return $this->where($where)->save($data);
    }
    //获取所有客户信息
    public function getAll($where=[]){
        return $this->where($where)->select();
    }
}
?>