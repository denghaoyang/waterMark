<?php
namespace app\index\controller;
use think\Controller;
use app\index\controller\Base;

use app\index\model\RecordModel;
use app\index\model\RecordLogModel;
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
        $this->assign("startTime",$startTime);
        $this->assign("endTime",$endTime);
        $this->assign("fileGuid",$fileGuid);
        return  $this->fetch("inList");
    }

    //水印导出记录
    public function outList($startTime=null,$endTime=null,$fileGuid=null){
        $recordLogModel = new RecordLogModel();

        $list = $recordLogModel->getOutList($startTime,$endTime,$fileGuid);

        $this->assign("list",$list);
        $this->assign("startTime",$startTime);
        $this->assign("endTime",$endTime);
        $this->assign("fileGuid",$fileGuid);
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

    public function tableGraph($fileGuid){
        $recordModel = new RecordModel();
        //获取文件的节点信息
        $list = $recordModel->getInList(null,null,$fileGuid);
        //拼接vis.js的配置参数
        $nodes = [];
        $nodeList = [];
        $edges = [];
        //整理出节点数据与路线数据
        foreach($list as $key=>$value){
            $sourceNode = ['label'=>$value['sourceNodeName'],'id'=>$value['sourceNodeGuid'],'x'=>0,'y'=>0,'shape'=>'box'];
            if (!in_array($sourceNode,$nodes)){
                $nodes[] = $sourceNode;
            }
            $destNode = ['label'=>$value['destNodeName'],'id'=>$value['destNodeGuid'],'x'=>0,'y'=>0,'shape'=>'box'];
            if (!in_array($destNode,$nodes)){
                $nodes[] = $destNode;
            }
            $edges[$key]['label'] = $key+1;
            $edges[$key]['from'] = $value['sourceNodeGuid'];
            $edges[$key]['to'] = $value['destNodeGuid'];
            $edges[$key]['style'] = 'arrow';
        }

        //获取节点ID数组
        foreach ($nodes as $key=>$value){
            $nodeList[$key] = $value['id'];
        }
        //将流转信息转为树形结构
        $tree = $this->arrayToForest($edges,"to","from");
        $this->getLevel($nodes,$tree,$nodeList);

        //将节点按照层级分类
        $levelList = [];
        foreach ($nodes as $value){
            $levelList[$value['level']][] = $value['id'];
        }
        //设置节点配置信息
        $colorList = ["#7AFEC6","#66B3FF","#FFA042","#FF5151","#CA8EFF","#6FB7B7"];
        foreach ($nodes as &$value){
            $value['color'] = $colorList[$value['level']];
            $value['x'] = 200*$value['level'];
            $value['y'] = 100*$this->getKey($value['id'],$levelList);
        }
        $this->assign("list",$list);
        $this->assign("nodes",json_encode($nodes));
        $this->assign("edges",json_encode($edges));
        return $this->fetch("tablegraph");
    }

    private function getKey($id,$levelList){
        foreach ($levelList as $value){
            foreach ($value as $key=>$val){
                if ($val==$id){
                    return $key;
                }
            }
        }
    }

    private function getLevel(&$data,$tree,$nodeList,$level=0){
        foreach($tree as $value){
            $key = array_search($value['from'],$nodeList);
            $data[$key]['level'] = $level;
            $key = array_search($value['to'],$nodeList);
            $data[$key]['level'] = $level+1;

            if (array_key_exists("children",$value)){
                $this->getLevel($data,$value['children'],$nodeList,++$level);
            }else{
                continue;
            }
        }
    }

    /**
     * $list 数组转化成森林
     * $pk key
     * $pid parentId
     * */
    private function arrayToForest($list, $pk, $pid, $child = 'children') {
        $tree = array();
        if (!is_array($list)) {
            return $tree;
        }
        $refer = array();
        $parentNodeIdArr = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
            $parentNodeIdArr[$data[$pid]] = $data[$pid];
        }
        /* 寻找根结点 */
        foreach ($list as $key => $data) {
            if (in_array($data[$pk], $parentNodeIdArr)) {
                unset($parentNodeIdArr[$data[$pk]]);
            }
        }
        foreach ($list as $key => $data) {
            $parantId = $data[$pid];
            if (in_array($parantId, $parentNodeIdArr)) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parantId])) {
                    $parent = &$refer[$parantId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
        return $tree;
    }

}
