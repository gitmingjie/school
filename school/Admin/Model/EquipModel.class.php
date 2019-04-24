<?php 

namespace Admin\Model;
use Think\Model;

class EquipModel extends Model
{	
	//定义验证规则
	 protected $_validate = array(     
            array('equip_name','require','设备名不能为空',0,'',3), //默认情况下用正则进行验证
            array('bind','require','绑定类型不能为空',0,'',3), //默认情况下用正则进行验证
	 	);
	

    
    //增加新设备
    public function addEquip($data){
        return $this->add($data);
    }
    //设备列表
    public function getList($page,$where,$order="equip_id",$keyword="desc"){
        return M('equip as e')->field('e.*,z.zname')
                    ->join('zone as z on z.zid = e.zid')
                    ->where($where)
                    ->order(array($order=>$keyword))
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
    }
    //获取设备信息
    public function getEquip($where){
        return $this->where($where)->find();
    }
    //修改设备信息
    public function editEquip($where,$data){
        return $this->where($where)->save($data);
    }
    //删除分区
    public function delEquip($where){
        return $this->where($where)->delete();
    }
    //启动记录
    public function getLogList($page,$where,$order="log_id",$keyword="desc"){
        return M('operate_log as o')->field('o.*,z.zname,e.equip_name')
                    ->join('zone as z on z.zid = o.zid')
                    ->join('equip as e on e.equip_id = o.equip_id')
                    ->where($where)
                    ->order(array($order=>$keyword))
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
    }
}
?>