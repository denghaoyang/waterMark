<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-2-4
 * Time: 16:28
 */
namespace app\index\model;

use think\Model;
use think\Db;

class RecordModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'wt_record';

    public function getListByFileId($fileId){

        return $this
            ->where("fileGuid",$fileId)
            ->field("guid,fileGuid,embedTime,sourceNodeGuid,destNodeGuid,userGuid,watermarkIndex,watermarkContent,remark")
            ->select();
    }

    public function saveList($data){
        return $this->validate(
            [
                'guid'  => 'unique:wt_record',
                'fileGuid'  => 'unique:wt_record',
            ],
            [
                'guid.unique' => 'guid参数重复',
                'fileGuid.unique' => 'fileGuid参数重复',
            ])->save($data);
    }

    public function getInList($startTime,$endTime,$fileGuid){
        $map = [];
        $map['n.is_del'] = 0;
        $map['n1.is_del'] = 0;
        if ($startTime){
            $map['r.embedTime'] = ['gt',$startTime];
        }
        if ($endTime){
           $map['r.embedTime'] = ['lt',$endTime];
        }
        if($fileGuid){
            $map['r.fileGuid'] = (int)$fileGuid;
        }
        return $this->alias("r")
                    ->join("wt_node n","r.sourceNodeGuid = n.guid")
                    ->join("wt_node n1","r.destNodeGuid = n1.guid")
                    ->join("wt_user u","r.userGuid = u.guid")
                    ->field("r.guid,r.fileGuid,r.embedTime,r.sourceNodeGuid,r.destNodeGuid,n.name as sourceNodeName,n1.name as destNodeName,u.name as userName,r.watermarkContent,r.watermarkIndex,r.remark,r.addtime")
                    ->order("addtime desc")
                    ->where($map)
                    ->paginate(10);
    }


    public function getNodeInfo($fileGuid){
        $map = [];
        $map['n.is_del'] = 0;
        $map['n1.is_del'] = 0;
        $map['r.fileGuid'] = $fileGuid;
        return $this->alias("r")
                    ->join("wt_node n","r.sourceNodeGuid = n.guid")
                    ->join("wt_node n1","r.destNodeGuid = n1.guid")
                    ->join("wt_user u","r.userGuid = u.guid")
                    ->field("n.name as sourceNodeName,n1.name as destNodeName,r.embedTime,r.sourceNodeGuid,r.destNodeGuid,u.name as userName,r.watermarkContent,r.watermarkIndex,r.remark")
                    ->order("embedTime")
                    ->where($map)
                    ->select();
    }
}