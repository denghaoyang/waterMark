<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22 0022
 * Time: 下午 7:30
 */
namespace app\index\controller;
use app\index\model\UserModel;
use think\Controller;
use app\index\controller\Base;

use app\index\model\RecordModel;
use app\index\model\RecordLogModel;

class Analyse extends Base{

    public function inList(){
        $recordModel = new RecordModel();
        $today = date("Y-m-d",time());
        $yesterday = date("Y-m-d",strtotime("-1 day"));
        $tomorrow = date("Y-m-d",strtotime("+1 day"));
        $lastMonth = date("Y-m-d",strtotime("-1 month"));


        //当日水印嵌入数量汇总
        $count = $recordModel->getCount($today,$tomorrow);
        //当日水印嵌入数量环比分析
        $yesterdayCount = $recordModel->getCount($yesterday,$today);
        //当日水印嵌入的文件字节数汇总
        $byteCount = $recordModel->getCount($today,$tomorrow,"byte");

        //拼接饼图数据
        $pieTableJson = $recordModel->getPieTableJsonDate($today,$lastMonth);

        //折线图数据
        $lineTableJson = $recordModel->getLineTableJson($today,$lastMonth);

        $this->assign("lineTableJson",$lineTableJson);
        $this->assign('pieTableJson',$pieTableJson);
        $this->assign('count',$count);
        $this->assign('yesterdayCount',$yesterdayCount);
        $this->assign('byteCount',$byteCount);
        return $this->fetch("inList");
    }

    public function outList(){
        $recordLogModel = new RecordLogModel();
        $today = strtotime(date("Y-m-d",time()));
        $yesterday = strtotime(date("Y-m-d",strtotime("-1 day")));
        $tomorrow = strtotime(date("Y-m-d",strtotime("+1 day")));
        $lastMonth = strtotime(date("Y-m-d",strtotime("-1 month")));

        //当日水印嵌入数量汇总
        $count = $recordLogModel->getCount($today,$tomorrow);
        //当日水印嵌入数量环比分析
        $yesterdayCount = $recordLogModel->getCount($yesterday,$today);
        //当日水印嵌入的文件字节数汇总
        $byteCount = $recordLogModel->getCount($today,$tomorrow,"byte");

        //拼接饼图数据
        $pieTableJson = $recordLogModel->getPieTableJsonDate($tomorrow,$lastMonth);

        //折线图数据
        $lineTableJson = $recordLogModel->getLineTableJson($tomorrow,$lastMonth);

        $this->assign("lineTableJson",$lineTableJson);
        $this->assign('pieTableJson',$pieTableJson);
        $this->assign('count',$count);
        $this->assign('yesterdayCount',$yesterdayCount);
        $this->assign('byteCount',$byteCount);
        return $this->fetch("outList");
    }

    public function changeData(){
        $recordLogModel = new RecordLogModel();
        $recordLogModel->saveData();
    }

}