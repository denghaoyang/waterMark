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

use app\index\model\NodeModel;


class Node extends Base
{
    public function index(){
        $nodeModel = new NodeModel();

        $list = $nodeModel->getList();

        $this->assign("list",$list);
        return $this->fetch();
    }

    public function add(){
        return $this->fetch();
    }

    public function doAdd(){
        $nodeModel = new NodeModel();
        $userModel = new UserModel();
        $data = input("post.");

        $addNodeStatus = $nodeModel->saveList($data);
        //插入用户数据
        $addUserStatus = $userModel->addUser($data);

        if (($addNodeStatus !== FALSE)&&($addUserStatus !== FALSE)){
            return $this->success("添加成功","/index/node/index");
        }else{
            return $this->error("添加失败,失败原因:".$nodeModel->getError());
        }
    }

    public function edit($id){
        $nodeModel = new NodeModel();

        $data = $nodeModel->findList($id);
        $this->assign("info",$data);

        return $this->fetch("edit");
    }

    public function doEdit(){
        $nodeModel = new NodeModel();

        $data = input("post.");

        $status = $nodeModel->updateList($data);

        if ($status !== FALSE){
            return $this->success("编辑成功","/index/node/index");
        }else{
            return $this->error("编辑失败");
        }
    }

    public function del($id){
        $nodeModel = new NodeModel();

        $data['is_del'] = 1;
        $data['id'] = $id;

        $status = $nodeModel->updateList($data);

        if ($status !== FALSE){
            return $this->redirect("/index/node/index");
        }else{
            return $this->error("删除失败");
        }
    }
}