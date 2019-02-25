<?php
namespace app\index\controller;
use think\Controller;
use think\Session;

class Base extends Controller
{
    public function _initialize()
    {
        //判断登录状态
        if (Session::has("userinfo")){
            $userInfo = Session::get("userinfo");
            $this->assign('userinfo',$userInfo);
        }else{
            $this->redirect("/index/login/login");
        }
        
        $this->view->engine->layout(true);
    }

    public function changeNullTo(&$data) {
        if (is_array($data)) {
            foreach ($data as &$val) {
                if (is_array($val) || is_object($val)) {
                    $val = is_object($val)?$val->toArray():$val;
                    $this->changeNullTo($val);
                } else {
                    if (is_null($val)) {
                        $val = "";
                    }
                }
            }
        } else {
            if($data == null) {
                $data = "";
            }
        }
        return $data;
    }

<<<<<<< HEAD
    public function changeIntToString(&$data) {
        if (is_array($data)) {
            foreach ($data as &$val) {
                if (is_array($val)) {
                    $this->changeIntToString($val);
                } else {
                    if(is_int($val)) {
                        $val = strval($val);
                    }
                }
            }
        } else {
            if(is_int($data)) {
                $data = strval($data);
            }
        }
        return $data;
    }
=======
>>>>>>> e542abea1c6012369bad76c831fe9f14607a1f1f

    function unicodeDecode($unicode_str){
        $json = '{"str":"'.$unicode_str.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }

}
