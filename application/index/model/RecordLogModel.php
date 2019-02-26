<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22 0022
 * Time: 下午 8:18
 */
namespace app\index\model;

use think\Model;
use think\Db;

class RecordLogModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'wt_record_log';

    public function getOutList($startTime,$endTime,$fileGuid){
        $map = [];
        $map['n.is_del'] = 0;
        $map['n1.is_del'] = 0;
        if ($startTime){
            $map['rl.addtime'] = ['gt',strtotime($startTime)];
        }
        if ($endTime){
            $map['rl.addtime'] = ['lt',strtotime($endTime)];
        }
        if($fileGuid){
            $map['r.fileGuid'] = (int)$fileGuid;
        }
        return $this->alias("rl")
            ->join("wt_record r","rl.recordId=r.guid")
            ->join("wt_node n","r.sourceNodeGuid = n.guid")
            ->join("wt_node n1","r.destNodeGuid = n1.guid")
            ->join("wt_user u","r.userGuid = u.guid")
            ->field("r.guid,r.fileGuid,n.name as sourceNodeName,n1.name as destNodeName,u.name as userName,r.watermarkContent,r.watermarkIndex,r.remark,rl.addtime")
            ->order("addtime desc")
            ->where($map)
            ->paginate(10,false,['query'=> ['startTime'=>$startTime,'endTime'=>$endTime,'fileGuid'=>$fileGuid]]);
    }
}