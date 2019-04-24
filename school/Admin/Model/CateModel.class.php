<?php 

namespace Admin\Model;
use Think\Model;

class CateModel extends Model
{	
	//定义验证规则
	 protected $_validate = array(     
	 		array('cname','require','分类名不能为空或已使用',0,'unique',1), //默认情况下用正则进行验证
	 		array('pid','require','父级分类不能为空',0,'',1), //默认情况下用正则进行验证
	 		array('desc','require','描述不能为空',0,'',1), //默认情况下用正则进行验证
	 	);
	//添加分类
    public function addCate($data){
    	return $this->add($data);
    }
    //查询所有分类
    public function allCate(){
        $mod = new Model();
        $sql = "SELECT * FROM cate ORDER BY CONCAT(pids,cid) ASC";
        return $mod->query($sql);
    }

    //分类列表
    public function getList($page,$where){
    	return $this->where($where)->order('CONCAT(pids,cid)')->limit($page->firstRow,$page->listRows)->select();
        // $sql = "SELECT * FROM type {$where} ORDER BY CONCAT(path,id) ASC {$limit}";
    }
    //查询单个分类
    public function getCate($where){
    	return $this->where($where)->find();
    }
    //修改分类
    public function editCate($where,$data){
        return $this->where($where)->save($data);
    }
    //删除分类
    public function delCate($where){
        return $this->where($where)->delete();
    }

}
?>