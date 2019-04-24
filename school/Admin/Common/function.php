<?php 
	//设置分页函数
	function setpage($table,$where,$page_num){
	    $mod = D($table);
	    //实例化分页类
	    $page = new \Think\Page($mod->where($where)->Count(),$page_num);
	    return $page;
	}
	//比较温度
	function  compareTemp($envir_id,$zid){
		$env_info = M('environment')->where(['envir_id'=>$envir_id])->find();
		$zone_info = M('zone')->where(['zid'=>$zid])->find();
		if($env_info['temp'] > ($zone_info['temp'] + $zone_info['temp_range'])){
			//温度过大
			$equip_info = M('equip')->where(['zid'=>$zid,'bind'=>'temp'])->find();
			if($equip_info && $equip_info['status'] == 0){
				//有绑定设备
				M('equip')->where(['equip_id'=>$equip_info['equip_id']])->save(['status'=>1]);
				$data = array(
						'zid'=>$zid,
						'equip_id'=>$equip_info['equip_id'],
						'operate'=>1,
						'reason'=>'温度过高',
						'time'=>time()
					);
				M('operate_log')->add($data);
				return 1;//报警并开启
			}else{
				return false;
			}
		}elseif($env_info['temp'] < ($zone_info['temp'] + $zone_info['temp_range'])){
			//温度低
			$equip_info = M('equip')->where(['zid'=>$zid,'bind'=>'temp'])->find();
			if($equip_info && $equip_info['status'] == 1){
				//有绑定设备
				M('equip')->where(['equip_id'=>$equip_info['equip_id']])->save(['status'=>0]);
				$data = array(
						'zid'=>$zid,
						'equip_id'=>$equip_info['equip_id'],
						'operate'=>0,
						'reason'=>'温度符合标准',
						'time'=>time()
					);
				M('operate_log')->add($data);
				return 2;
			}else{
				return false;
			}
		}
	}
		//比较湿度
	function  compareHum($envir_id,$zid){
		$env_info = M('environment')->where(['envir_id'=>$envir_id])->find();
		$zone_info = M('zone')->where(['zid'=>$zid])->find();
		if($env_info['humidness'] > ($zone_info['humidness'] + $zone_info['humidness_range'])){
			//温度过大
			$equip_info = M('equip')->where(['zid'=>$zid,'bind'=>'humidness'])->find();
			if($equip_info && $equip_info['status'] == 0){
				//有绑定设备
				M('equip')->where(['equip_id'=>$equip_info['equip_id']])->save(['status'=>1]);
				$data = array(
						'zid'=>$zid,
						'equip_id'=>$equip_info['equip_id'],
						'operate'=>1,
						'reason'=>'湿度过大',
						'time'=>time()
					);
				M('operate_log')->add($data);
				return 1;//报警并开启
			}else{
				return false;
			}
		}elseif($env_info['humidness'] < ($zone_info['humidness'] + $zone_info['humidness_range'])){
			//温度低
			$equip_info = M('equip')->where(['zid'=>$zid,'bind'=>'humidness'])->find();
			if($equip_info && $equip_info['status'] == 1){
				//有绑定设备
				M('equip')->where(['equip_id'=>$equip_info['equip_id']])->save(['status'=>0]);
				$data = array(
						'zid'=>$zid,
						'equip_id'=>$equip_info['equip_id'],
						'operate'=>0,
						'reason'=>'湿度符合标准',
						'time'=>time()
					);
				M('operate_log')->add($data);
				return 2;
			}else{
				return false;
			}
		}
	}
		//比较烟雾
	function  compareSmog($envir_id,$zid){
		$env_info = M('environment')->where(['envir_id'=>$envir_id])->find();
		$zone_info = M('zone')->where(['zid'=>$zid])->find();
		if($env_info['smog'] > ($zone_info['smog'] + $zone_info['smog_range'])){
			//温度过大
			$equip_info = M('equip')->where(['zid'=>$zid,'bind'=>'smog'])->find();
			if($equip_info && $equip_info['status'] == 0){
				//有绑定设备
				M('equip')->where(['equip_id'=>$equip_info['equip_id']])->save(['status'=>1]);
				$data = array(
						'zid'=>$zid,
						'equip_id'=>$equip_info['equip_id'],
						'operate'=>1,
						'reason'=>'烟雾浓度过高',
						'time'=>time()
					);
				M('operate_log')->add($data);
				return 1;//报警并开启
			}else{
				return false;
			}
		}elseif($env_info['smog'] < ($zone_info['smog'] + $zone_info['smog_range'])){
			//温度低
			$equip_info = M('equip')->where(['zid'=>$zid,'bind'=>'smog'])->find();
			if($equip_info && $equip_info['status'] == 1){
				//有绑定设备
				M('equip')->where(['equip_id'=>$equip_info['equip_id']])->save(['status'=>0]);
				$data = array(
						'zid'=>$zid,
						'equip_id'=>$equip_info['equip_id'],
						'operate'=>0,
						'reason'=>'烟雾浓度符合标准',
						'time'=>time()
					);
				M('operate_log')->add($data);
				return 2;
			}else{
				return false;
			}
		}
	}
		//比较光照
	function  compareSun($envir_id,$zid){
		$env_info = M('environment')->where(['envir_id'=>$envir_id])->find();
		$zone_info = M('zone')->where(['zid'=>$zid])->find();
		if($env_info['sun'] > ($zone_info['sun'] + $zone_info['sun_range'])){
			//温度过大
			$equip_info = M('equip')->where(['zid'=>$zid,'bind'=>'sun'])->find();
			if($equip_info && $equip_info['status'] == 0){
				//有绑定设备
				M('equip')->where(['equip_id'=>$equip_info['equip_id']])->save(['status'=>1]);
				$data = array(
						'zid'=>$zid,
						'equip_id'=>$equip_info['equip_id'],
						'operate'=>1,
						'reason'=>'光照强度过高',
						'time'=>time()
					);
				M('operate_log')->add($data);
				return 1;//报警并开启
			}else{
				return false;
			}
		}elseif($env_info['sun'] < ($zone_info['sun'] + $zone_info['sun_range'])){
			//温度低
			$equip_info = M('equip')->where(['zid'=>$zid,'bind'=>'sun'])->find();
			if($equip_info && $equip_info['status'] == 1){
				//有绑定设备
				M('equip')->where(['equip_id'=>$equip_info['equip_id']])->save(['status'=>0]);
				$data = array(
						'zid'=>$zid,
						'equip_id'=>$equip_info['equip_id'],
						'operate'=>0,
						'reason'=>'光照强度符合标准',
						'time'=>time()
					);
				M('operate_log')->add($data);
				return 2;
			}else{
				return false;
			}
		}
	}
	
?>