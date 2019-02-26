<?php
namespace app\index\controller;
use think\Controller;
use think\Session;

class Base extends Controller
{
    protected $userinfo;
    public function _initialize()
    {
        //判断登录状态
        if (Session::has("userinfo")){
            $userInfo = Session::get("userinfo");
            $this->userinfo = $userInfo;
            $this->assign('userinfo',$userInfo);
        }else{
            $this->redirect("/index/login/login");
        }
        
        $this->view->engine->layout(true);
    }
}
