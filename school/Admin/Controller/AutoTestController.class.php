<?php
namespace Admin\Controller;
use Think\Controller;
class AutoTestController extends Controller {

	//负责人列表页
    public function testOne(){
        $data = array(
            'ip'=>$_SERVER["REMOTE_ADDR"],
            'content'=>'这是一个测试自动任务的日志'.date("Y-m-d H:i:s"),
            'add_time'=>time()
        );
        M('auto_test')->add($data);
    }
}