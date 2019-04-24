<?php 

namespace Admin\Model;
use Think\Model;

class ZoneModel extends Model
{	
	//定义验证规则
	 protected $_validate = array(     
	 		array('zname','require','分区名不能为空',0,'',1), //默认情况下用正则进行验证
	 		array('temp','/^([0-9]*)|(([0-9]*).([0-9]*))$/','温度不能为空',0,'',3), //默认情况下用正则进行验证
            array('humidness','/^([0-9]*)|(([0-9]*).([0-9]*))$/','湿度不能为空',0,'',3), //默认情况下用正则进行验证
            array('smog','/^([0-9]*)|(([0-9]*).([0-9]*))$/','烟雾浓度不能为空',0,'',3), //默认情况下用正则进行验证
            array('sun','/^([0-9]*)|(([0-9]*).([0-9]*))$/','光照强度不能为空',0,'',3), //默认情况下用正则进行验证
            array('temp_range','/^([0-9]*)|(([0-9]*).([0-9]*))$/','温度误差不能为空',0,'',3), //默认情况下用正则进行验证
            array('humidness_range','/^([0-9]*)|(([0-9]*).([0-9]*))$/','湿度误差不能为空',0,'',3), //默认情况下用正则进行验证
            array('smog_range','/^([0-9]*)|(([0-9]*).([0-9]*))$/','烟雾浓度误差不能为空',0,'',3), //默认情况下用正则进行验证
            array('sun_range','/^([0-9]*)|(([0-9]*).([0-9]*))$/','光照强度误差不能为空',0,'',3), //默认情况下用正则进行验证
            array('wid','require','请选择仓库',0,'',1), //默认情况下用正则进行验证
	 		array('all_zarea','/^([0-9]{1,9})$/','分区总面积不能为空',0,'',3), //默认情况下用正则进行验证
	 	);
	

    //分区列表
    public function zoneIndex($page,$where,$order="zid",$keyword="desc"){
        return M('zone as z')->field('z.*,w.wname')
                            ->join('ware as w on w.wid = z.wid')
                            ->where($where)
                            ->order(array($order=>$keyword))
                            ->limit($page->firstRow,$page->listRows)
                            ->select();
    }
    //增加环境的数据
    public function addEnvironment(){
        $data = array(
            'temp'=>0,
            'humidness'=>0,
            'sun'=>0,
            'smog'=>0,
            );
        return M('environment')->add($data);
    }
    //增加新分区
    public function addZone($data){
        $data['envir_id'] = $this->addEnvironment();
        return $this->add($data);
    }
    //获取分区信息
    public function getZone($where){
        return $this->where($where)->find();
    }
    //修改分区
    public function editZone($where,$data){
        return $this->where($where)->save($data);
    }
    //查询该仓库下的总面积
    public function sumZarea($where){
        return $this->where($where)->sum('all_zarea');
    }
    //查询该仓库下的总面积
    public function countZarea($where){
        return $this->where($where)->count();
    }

    //删除分区
    public function delZone($where){
        return $this->where($where)->delete();
    }
    //获取全部分区
    public function getAll($where){
        return $this->where($where)->select();
    }

}
?>