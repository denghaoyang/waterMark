<?php
namespace app\index\controller;
use think\Controller;

use app\index\model\UserModel;
use think\Session;

class Login extends Controller
{
    public function login()
    {
        return $this->fetch("/index/login");
    }

    public function doLogin($username,$pwd){
        $userModel = new UserModel();

        $list = $userModel->checkUsernameAndPwd($username,$pwd);
        if ($list){
            Session::set("userinfo",$list);
            return $this->success();
        }else{
            return $this->error("用户名和密码错误");
        }

    }

    public function logout(){
        Session::clear();
        return $this->redirect("login");
    }
}
