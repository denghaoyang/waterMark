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

    public function getInList($startTime,$endTime,$fileGuid,$type){
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
            $map['r.fileGuid'] = $fileGuid;
        }
        if ($type!=null){
            $map['r.type'] = $type;
        }
        return $this->alias("r")
                    ->join("wt_node n","r.sourceNodeGuid = n.guid")
                    ->join("wt_node n1","r.destNodeGuid = n1.guid")
                    ->join("wt_user u","r.userGuid = u.guid")
                    ->field("r.guid,r.type,r.fileGuid,r.embedTime,r.sourceNodeGuid,r.destNodeGuid,n.name as sourceNodeName,n1.name as destNodeName,u.name as userName,r.watermarkContent,r.watermarkIndex,r.remark,r.addtime")
                    ->order("embedTime desc")
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

    public function getCount($today,$tomorrow,$column=null){
        $map['embedTime'] = ['between',"$today,$tomorrow"];

        if ($column){
            return $this->where($map)->sum($column);
        }else{
            return $this->where($map)->count();
        }
    }

    public function getPieTableJsonDate($today,$lastMonth){
        $map['embedTime'] = ['between',"$lastMonth,$today"];

        $result = [];
        $result[] = ['label'=>"嵌入成功",'color'=>'#bababa','data'=>$this->where($map)->where(['status'=>0])->count()];
        $result[] = ['label'=>"待嵌入",'color'=>'#79d2c0','data'=>$this->where($map)->where(['status'=>1])->count()];
        $result[] = ['label'=>"取消嵌入",'color'=>'#1ab394','data'=>$this->where($map)->where(['status'=>2])->count()];

        return json_encode($result);
    }

    public function getLineTableJson($today,$lastMonth){
        $map['embedTime'] = ['between',"$lastMonth,$today"];

        $result = $this->where($map)->group("DATE_FORMAT(embedTime, '%Y-%m-%d')")->field("DATE_FORMAT(embedTime,'%Y-%m-%d') as day,COUNT(1) as value")->select();
        return json_encode($result);
    }

    //清理重复的节点数据
    public function saveData(){
//        $fileGuids = $this->group("fileGuid")->column("fileGuid");
//        foreach($fileGuids as $fileGuid){
//            $recordList = $this->where('fileGuid',$fileGuid)->select();
//            foreach ($recordList as $value){
//                if ($value['sourceNodeGuid'] == $value['destNodeGuid']){
//                    $value['destNodeGuid'] = rand(1,30);
//                    $this->update($value->toArray(),['id'=>$value['id']]);
//                }
//                $map = [];
//                $map['sourceNodeGuid'] = $value['destNodeGuid'];
//                $map['destNodeGuid'] = $value['sourceNodeGuid'];
//                $map['fileGuid'] = $value['fileGuid'];
//                $list = $this->where($map)->find();
//                if($list){
//                    $value['destNodeGuid'] = rand(1,30);
//                    $this->update($value->toArray(),['id'=>$value['id']]);
//                }
//            }
//        }
        echo "success";
    }
}