<?php 

namespace Admin\Model;
use Think\Model;

class GoodsModel extends Model
{	
	//定义验证规则
     protected $_validate = array(     
            array('gname','require','货物名不能为空',0,'',3), //默认情况下用正则进行验证
            array('gsum','/^[1-9]{1}\d{0,9}$/','供应数量不符合要求',0,'',3), //默认情况下用正则进行验证
            array('in_price','/(^[1-9]\d*(\.\d{1,2})?$)|(^0(\.\d{1,2})?$)/','供应价格不符合要求',0,'',3), //默认情况下用正则进行验证
            array('zarea','/(^[1-9]\d*(\.\d{1,2})?$)|(^0(\.\d{1,2})?$)/','规格不符合要求',0,'',3), //默认情况下用正则进行验证
            array('zid','require','请选择仓库分区',0,'require',3),
        );
	//获取所有职位
    public function addGoods($data){
        return $this->add($data);
    }
    //查询单个信息
    public function getGoods($where){
        return $this->where($where)->find();
    }
    //增加货物数量
    public function upGoods($where,$num){
        return $this->where($where)->setInc('gsum',$num);
    }
    //减小货物数量
    public function downGoods($where,$num){
        return $this->where($where)->setDec('gsum',$num);
    }
    ////货物列表
    public function getList($page,$where,$order="gid",$keyword="desc"){
        return M('goods as s')->field('s.*,z.zname,p.spname')
                    ->join('zone as z on z.zid = s.zid')
                    ->join('supply as p on p.spid = s.spid')
                    ->where($where)
                    ->order(array($order=>$keyword))
                    ->limit($page->firstRow,$page->listRows)
                    ->select();
    }
    //修改信息
    public function editGoods($where,$data){
        return $this->where($where)->save($data);
    }
}
?>