<?php 

namespace Admin\Model;
use Think\Model;

class WareModel extends Model
{	
	//定义验证规则
	 protected $_validate = array(     
	 		array('wname','require','仓库名不能为空或重复',0,'unique',1), //默认情况下用正则进行验证
	 		array('warea','/^([0-9]*)|(([0-9]*).([0-9]*))$/','仓库面积不能为空',0,'',3), //默认情况下用正则进行验证
	 		array('zum','/^([0-9]{1,9})$/','仓库数量不能为空',0,'',3), //默认情况下用正则进行验证
	 	);
	//添加仓库
    public function addWare($data){
    	return $this->add($data);
    }

    //仓库列表
    public function getList($page,$where,$order="wid",$keyword="desc"){
        
    	return M('ware as w')->field('w.*,c.cname')
                    ->join('cate as c on c.cid = w.cid')
                    ->where($where)
                    ->order(array($order=>$keyword))
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
    }
    //查询仓库
    public function getWare($where){
    	return $this->where($where)->find();
    }

    //修改仓库信息
    public function editWare($where,$data){
        return $this->where($where)->save($data);
    }
    //所有仓库
    public function getAll($where=[]){
        return $this->where($where)->select();
    }

    
}
?>