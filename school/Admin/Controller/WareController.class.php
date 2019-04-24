<?php
namespace Admin\Controller;
use Think\Controller;
class WareController extends CommonController {

	//仓库列表页
    public function index(){
        $ware = D("ware"); // 实例化对象
        $where = [];
        $page = setpage('ware',$where,5);
        $order = isset($_GET['order'])?$_GET['order']:'wid';
        $desc = isset($_GET['asc'])?$_GET['asc']:'desc';
        //分页跳转的时候保证查询条件
        if($_GET){
            $wname = I('get.wname','','trim');
            if($wname){
                $where['wname'] = array('like','%'.$wname.'%');
            }
            $cname = I('get.cname','','trim');
            if($wname){
                $where['c.cname'] = array('like','%'.$cname.'%');
            }
            // dump($where);die;
            $page = setpage('ware',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                if($key != 'znum' && $key != 'asc'){
                    $where_str .= "$key=".urlencode($val).'&';
                }
            }
            
        }
        $list = $ware->getList($page,$where,$order,$desc);
         // dump($list);die;
        $this->assign('list',$list);
        $this->assign('where_str',$where_str);
        $this->assign('page',$page->show());
        $this->assign('open_ware','nav-active');
        $this->display();
    }

    //增加仓库
    public function addWare(){
        $cate = D("Cate"); // 实例化对象
        $allCate = $cate->allCate();
        // dump($allCate);die;
        if($_POST){
            // dump($_POST);die;
            $data['cid'] = I('post.cid',0,'_int');
            $data['cid'] > 0 || $this->error('请选择分类');
            // dump($_POST['wname']);die;
            $data['wname'] = I('post.wname','','htmltotxt');
            // dump($data['wname']);die;
            $data['wname'] || $this->error('仓库名不能为空');
            $data['warea'] = I('post.warea',0,'_int');
            $data['warea'] >0 || $this->error('仓库面积不能为空');
            $data['znum'] = I('post.znum',0,'_int');
            $data['znum']> 0 || $this->error('分区数量不能为空');
            $ware = D("ware"); // 实例化对象
            if (!$ware->create($data)){ 
                $this->error($ware->getError(),U('Ware/addWare'));    
            }else{
                $res = $ware->addWare($data);
                if($res){
                    $this->success('添加仓库成功',U('Ware/index'));die;
                }else{
                    $this->error('添加失败');
                }
            }
        }
        $this->assign('allCate',$allCate);
    	$this->assign('open_ware','nav-active');
    	$this->display();
    }
    //修改仓库信息
    public function editWare(){
        if($_GET){
            $wid = I('get.wid',0,'_int');
            $wid > 0 || $this->error('选择有误,请重新选择');
            $ware = D('ware');
            $info = $ware->getWare(['wid'=>$wid]);
            if(!$info){
                $this->error('该仓库不存在,请重新选择');
            }
        }
        
        if($_POST){
             // dump($_POST);die;
            $wid = I('post.wid',0,'_int');
            $wid > 0 || $this->error('选择有误,请重新选择');
            $data['cid'] = I('post.cid',0,'_int');
            $data['cid'] > 0 || $this->error('请选择分类');
            // dump($_POST['wname']);die;
            $data['wname'] = I('post.wname','','htmltotxt');
            // dump($data['wname']);die;
            $data['wname'] || $this->error('仓库名不能为空');
            $data['warea'] = I('post.warea',0,'_int');
            $data['warea'] >0 || $this->error('仓库面积不能为空');
            $data['znum'] = I('post.znum',0,'_int');
            $data['znum']> 0 || $this->error('分区数量不能为空');
            $zone = D("zone"); // 实例化对象
            //查询改仓库下的分区总面积
            $sum = $zone->sumZarea(['wid'=>$wid]);
            $count = $zone->countZarea(['wid'=>$wid]);

            $ware = D('ware');
            // dump($info['znum']);die;
            if($data['znum'] < $count){
                $this->error('您当前分区数量过小');
            }
            if($data['warea'] < $sum ){
                $this->error('您的分区总面积大于仓库面积');
            }
            if (!$ware->create($data,2)){ 
                $this->error($ware->getError(),U('Ware/addWare'));    
            }else{
                $res = $ware->editWare(['wid'=>$wid],$data);
                if($res){
                    $this->success('修改仓库成功',U('Ware/index'));die;
                }else{
                    $this->error('修改失败');
                }
            }
        }
        $cate = D("Cate"); // 实例化对象
        $allCate = $cate->allCate();
        $this->assign('allCate',$allCate);
        $this->assign('info',$info);
        $this->assign('open_ware','nav-active');
        $this->display();
    }

    //查看分区
    public function zoneIndex(){
        $wid = I('get.wid',0,'_int');
        $wid > 0 || $this->error('选择有误,请重新选择');
        $zone = D('zone');
        $where['wid'] = $wid;
        $order = isset($_GET['order'])?$_GET['order']:'zid';
        $desc = isset($_GET['asc'])?$_GET['asc']:'desc';
        $page = setpage('zone',$where,5);
        //分页跳转的时候保证查询条件
        if($_GET){
            $where1['z.wid'] = $wid;
            $zname = I('get.zname','','trim');
            if($zname){
                $where1['zname'] = array('like','%'.$zname.'%');
                $where['zname'] = array('like','%'.$zname.'%');
            }
            $page = setpage('zone',$where,5);
            foreach($_GET as $key=>$val) {
                $Page->parameter   .=   "$key=".urlencode($val).'&';
                if($key != 'all_zarea' && $key != 'asc'){
                    $where_str .= "$key=".urlencode($val).'&';
                }
            }
            
        }
        $list = $zone->zoneIndex($page,$where1,$order,$desc);
         // dump($list);die;
        $this->assign('list',$list);
        $this->assign('wid',$wid);
        $this->assign('where_str',$where_str);
        $this->assign('page',$page->show());
        $this->assign('open_ware','nav-active');
        $this->display();
    }

    //添加分区
    public function addZone(){
        if($_GET){
            $wid = I('get.wid',0,'_int');
            $wid > 0 || $this->error('选择有误,请重新选择');
            $ware = D('ware');
            $info = $ware->getWare(['wid'=>$wid]);
        }
        if($_POST){
            // dump($_POST);die;
            $data['zname'] = I('post.zname','','htmltotxt');
            $data['zname']  || $this->error('分区名不能为空');
            $data['temp'] = I('post.temp',0,'_int');
            $data['temp'] > 0 || $this->error('请填写温度');
            $data['temp_range'] = I('post.temp_range',0,'_int');
            $data['temp_range'] > 0 || $this->error('请填写温度误差');
            $data['humidness'] = I('post.humidness',0,'_int');
             $data['humidness'] > 0 || $this->error('请填写湿度');
            $data['humidness_range'] = I('post.humidness_range',0,'_int');
            $data['humidness_range'] > 0 || $this->error('请填写湿度误差');
            $data['smog'] = I('post.smog',0,'_int');
            $data['smog'] > 0 || $this->error('请填写烟雾浓度');
            $data['smog_range'] = I('post.smog_range',0,'_int');
            $data['smog_range'] > 0 || $this->error('请填写烟雾浓度误差');
            $data['sun'] = I('post.sun',0,'_int');
            $data['sun'] > 0 || $this->error('请填写光照强度');
            $data['sun_range'] = I('post.sun_range',0,'_int'); 
            $data['sun_range'] > 0 || $this->error('请填写光照强度误差');
            $data['all_zarea'] = I('post.all_zarea',0,'_int'); 
            $data['all_zarea'] > 0 || $this->error('请填写分区总面积');
            $data['do_zarea'] = $data['all_zarea'];
            $data['wid'] = I('post.wid',0,'_int'); 
            $data['wid'] > 0 || $this->error('请选择仓库');
            // dump($data);die;
            $zone = D("zone"); // 实例化对象
            //查询改仓库下的分区总面积
            $sum = $zone->sumZarea(['wid'=>$data['wid']]);
            $count = $zone->countZarea(['wid'=>$data['wid']]);

            $ware = D('ware');
            $info = $ware->getWare(['wid'=>$data['wid']]);
            // dump($info['znum']);die;
            if($info['znum'] < ($count + 1)){
                $this->error('您当前分区数量已满');
            }
            if($info['warea'] < ($sum + $data['all_zarea'])){
                $this->error('分区总面积大于仓库面积');
            }
            if (!$zone->create($data)){ 
                $this->error($zone->getError(),U('ware/addZone',['wid'=>$data['wid']]));    
            }else{
                $res = $zone->addZone($data);
                if($res){
                    $this->success('添加仓库分区成功',U('ware/zoneIndex',['wid'=>$data['wid']]));die;
                }else{
                    $this->error('添加失败');
                }
            }
        }
        $this->assign('info',$info);
        $this->assign('open_ware','nav-active');
        $this->display();
    }
    //修改分区
    public function editZone(){
        if($_GET){
            $zid = I('get.zid',0,'_int');
            $zid > 0 || $this->error('选择有误,请重新选择');
            $zone = D('zone');
            $info = $zone->getZone(['zid'=>$zid]);
        }
        if($_POST){
            // dump($_POST);die;
            $zid = I('post.zid',0,'_int');
            $zid > 0 || $this->error('选择有误');
            $wid = I('post.wid',0,'_int');
            $wid > 0 || $this->error('选择有误');
            $data['zname'] = I('post.zname','','htmltotxt');
            $data['zname']  || $this->error('分区名不能为空');
            $data['temp'] = I('post.temp',0,'_int');
            $data['temp'] > 0 || $this->error('请填写温度');
            $data['temp_range'] = I('post.temp_range',0,'_int');
            $data['temp_range'] > 0 || $this->error('请填写温度误差');
            $data['humidness'] = I('post.humidness',0,'_int');
             $data['humidness'] > 0 || $this->error('请填写湿度');
            $data['humidness_range'] = I('post.humidness_range',0,'_int');
            $data['humidness_range'] > 0 || $this->error('请填写湿度误差');
            $data['smog'] = I('post.smog',0,'_int');
            $data['smog'] > 0 || $this->error('请填写烟雾浓度');
            $data['smog_range'] = I('post.smog_range',0,'_int');
            $data['smog_range'] > 0 || $this->error('请填写烟雾浓度误差');
            $data['sun'] = I('post.sun',0,'_int');
            $data['sun'] > 0 || $this->error('请填写光照强度');
            $data['sun_range'] = I('post.sun_range',0,'_int'); 
            $data['sun_range'] > 0 || $this->error('请填写光照强度误差');
            $data['all_zarea'] = I('post.all_zarea',0,'_int'); 
            $data['all_zarea'] > 0 || $this->error('请填写分区总面积');
            $data['do_zarea'] = $data['all_zarea'];
            // dump($data);die;
            $zone = D("zone"); // 实例化对象
            //查询改仓库下的分区总面积
            $sum = $zone->sumZarea(['wid'=>$wid]);
            $ware = D('ware');
            $info = $ware->getWare(['wid'=>$wid]);
            // dump($info['znum']);die;
            if($info['warea'] < ($sum + $data['all_zarea'])){
                $this->error('分区总面积大于仓库面积');
            }
            if (!$zone->create($data,2)){ 
                $this->error($zone->getError(),U('ware/editZone',['zid'=>$zid]));    
            }else{
                $res = $zone->editZone(['zid'=>$zid],$data);
                if($res){
                    $this->success('修改仓库分区成功',U('ware/zoneIndex',['wid'=>$wid]));die;
                }else{
                    $this->error('修改失败');
                }
            }
        }
        $this->assign('info',$info);
        $this->assign('open_ware','nav-active');
        $this->display();
    }

    //删除分区
    public function delZone(){
        $zid = I('post.zid',0,'_int');
        $zid > 0 || $this->error('选择有误，请重新选取');
        $zone = D('zone');
        $res = $zone->delZone(['zid'=>$zid]);
        if($res){
            echo 1;
        }else{
            echo 0;
        }
    }

    //绑定设备
    public function bindEquip(){
        
    }
    //模拟环境添加
    public function test(){
        $ware = D('ware');
        $ware_list = $ware->getAll();
         
        if($_POST){
            $zone = D('zone');
            $zid = I('post.zid',0,'_int');
            $data['temp'] = I('post.temp',0,'_int');
            $data['humidness'] = I('post.humidness',0,'_int');
            $data['smog'] = I('post.smog',0,'_int');
            $data['sun'] = I('post.sun',0,'_int');
            $info = $zone->getZone(['zid'=>$zid]);
            M('environment')->where(['envir_id'=>$info['envir_id']])->save($data);
            //比较环境
            $temp = compareTemp($info['envir_id'],$zid);
            if($temp){
                if($temp == 1){
                    echo '<script>alert("温度过高,降温设备已开启")</script>';
                }elseif($temp == 2){
                    echo '<script>alert("温度正常,降温设备关闭")</script>';
                }
            }
            $hum = compareHum($info['envir_id'],$zid);
            if($hum){
                if($hum == 1){
                    echo '<script>alert("湿度过高,除湿设备已开启")</script>';
                }elseif($hum == 2){
                    echo '<script>alert("湿度正常,除湿设备关闭")</script>';
                }
            }
            $smog = compareSmog($info['envir_id'],$zid);
            if($smog){
                if($smog == 1){
                    echo '<script>alert("烟雾浓度过高,除尘设备已开启")</script>';
                }elseif($smog == 2){
                    echo '<script>alert("烟雾浓度正常,除尘设备关闭")</script>';
                }
            }
            $sun = compareSun($info['envir_id'],$zid);
            if($sun){
                if($sun == 1){
                    echo '<script>alert("光照强度过高,遮光设备已开启")</script>';
                }elseif($sun == 2){
                    echo '<script>alert("光照强度正常,遮光设备关闭")</script>';
                }
            }

            
        }
        $this->assign('ware_list',$ware_list);
        $this->assign('open_ware','nav-active');
        $this->display();
    }
}