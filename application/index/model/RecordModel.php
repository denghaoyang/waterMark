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
        return $this->where("fileGuid",$fileId)->field("addtime",true)->select();
    }

    public function saveList($data){
        return $this->save($data);
    }

    public function getInList(){
        return $this->alias("r")
                    ->join("wt_node n","r.sourceNodeGuid = n.guid")
                    ->join("wt_user u","r.userGuid = u.guid")
                    ->field("r.guid,r.embedTime,n.name as nodeName,u.name as userName,r.watermarkContent,r.addtime")
                    ->order("addtime desc")
                    ->where(['n.is_del'=>0])
                    ->select();
    }
}