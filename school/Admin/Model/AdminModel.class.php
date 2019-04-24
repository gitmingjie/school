<?php 

namespace Admin\Model;
use Think\Model;

class AdminModel extends Model
{	
	//定义验证规则
	 protected $_validate = array(     
	 		array('admin_name','require','管理员名不能为空或已使用',0,'unique',1), //默认情况下用正则进行验证
	 		array('admin_phone','/^(1[34578]{1}\d{9})|(\d{3,4}-\d{7,8})$/','联系方式有误或已注册',0,'',3), //默认情况下用正则进行验证
	 		array('posid','require','请选择管理类型',0,'',3), //默认情况下用正则进行验证
            array('pass_name','require','登录名不能为空或已注册',0,'unique',1), //默认情况下用正则进行验证
            array('password','/^\w{4,8}$/','密码为空或格式错误',0,'require',3),
            array('repassword','password','确认密码不正确',0,'confirm',3), // 验证确认密码是否和密码一致
	 	);
	//获取所有职位
     public function getPos($where=[]){
        return M('pos')->where($where)->select();
     }
     //添加管理员
     public function addAdmin($data){
        return $this->add($data);
     }
     //管理员列表
     public function getList($page,$where,$order="admin_id",$keyword="desc"){
        return M('admin as a')->field('a.*,p.upos')
                    ->join('pos as p on p.posid = a.posid')
                    ->where($where)
                    ->order(array($order=>$keyword))
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
     }
     //获取admin信息
     public function getAdmin($where){
        return $this->where($where)->find();
     }
     //修改信息
     public function editAdmin($where,$data){
        return $this->where($where)->save($data);
     }
     //删除
     public function delAdmin($where){
        return $this->where($where)->delete();
     }
}
?>