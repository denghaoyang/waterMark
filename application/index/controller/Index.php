<?php
namespace app\index\controller;
use think\Controller;
use app\index\controller\Base;

use app\index\model\RecordModel;

use app\index\model\UserModel;
use think\Session;

class Index extends Base
{
    public function index(){
        return $this->fetch();
    }

    //水印嵌入记录
    public function inList($startTime=null,$endTime=null,$fileGuid=null){
        $recordModel = new RecordModel();

        $list = $recordModel->getInList($startTime,$endTime,$fileGuid);

        $this->assign("list",$list);

        return  $this->fetch("inList");
    }

    //水印导出记录
    public function outList(){
        return  $this->fetch("outList");
    }


    public function changePwd(){
        return $this->fetch("changePwd");
    }

    public function doChangePwd($oldPwd,$newPwd){
        $userModel = new UserModel();
        if ($this->userinfo['pwd']!=$oldPwd){
            return $this->error("输入的旧密码不正确");
        }else{
            if ($userModel->changePwd($newPwd,$this->userinfo['id'])!==false){
                //更新session
                Session::set("userinfo",$userModel->checkUsernameAndPwd($this->userinfo['username'],$newPwd));
                return $this->success();
            }else{
                return $this->error("修改密码失败");
            }
        }
    }
}
