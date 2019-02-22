<?php
namespace app\index\controller;
use think\Controller;
use app\index\controller\Base;

use app\index\model\IndexModel;


class Index extends Base
{
    public function index(){
        return $this->fetch();
    }

    //水印嵌入记录
    public function inList(){
        $this->view->engine->layout(true);
        return  $this->fetch("inList");
    }

    //水印导出记录
    public function outList(){
        return  $this->fetch("outList");
    }
}
