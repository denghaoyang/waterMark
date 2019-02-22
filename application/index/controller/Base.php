<?php
namespace app\index\controller;
use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {
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


    function unicodeDecode($unicode_str){
        $json = '{"str":"'.$unicode_str.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }

}
