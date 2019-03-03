<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22 0022
 * Time: 下午 8:18
 */
namespace app\index\model;

use think\Model;
use app\index\model\RecordModel;
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

    public function getCount($today,$tomorrow,$column=null){
        $map['rl.addtime'] = ['between',"$today,$tomorrow"];

        if ($column){
            return $this->alias("rl")
                    ->join("wt_record r",'r.guid=rl.recordId')
                    ->where($map)->sum($column);
        }else{
            return $this->alias("rl")->where($map)->count();
        }
    }

    public function getPieTableJsonDate($today,$lastMonth){
        $map['rl.addtime'] = ['between',"$lastMonth,$today"];

        $result = [];
        $result[] = ['label'=>"嵌入成功",'color'=>'#bababa','data'=>$this->alias("rl")->join("wt_record r",'r.guid=rl.recordId')->where($map)->where(['r.status'=>0])->count()];
        $result[] = ['label'=>"待嵌入",'color'=>'#79d2c0','data'=>$this->alias("rl")->join("wt_record r",'r.guid=rl.recordId')->where($map)->where(['r.status'=>1])->count()];
        $result[] = ['label'=>"取消嵌入",'color'=>'#1ab394','data'=>$this->alias("rl")->join("wt_record r",'r.guid=rl.recordId')->where($map)->where(['r.status'=>2])->count()];

        return json_encode($result);
    }

    public function getLineTableJson($today,$lastMonth){
        $map['rl.addtime'] = ['between',"$lastMonth,$today"];

        $result = $this->alias("rl")->join("wt_record r",'r.guid=rl.recordId')->where($map)->group("DATE_FORMAT(FROM_UNIXTIME(rl.addtime),'%Y-%m-%d')")->field("DATE_FORMAT(FROM_UNIXTIME(rl.addtime),'%Y-%m-%d') as day,COUNT(1) as value")->select();
        return json_encode($result);
    }

    public function saveData(){
        $recordModel = new RecordModel();

        $list = $recordModel->select();

        foreach ($list as $value){
            $i = rand(1,10);
            for ($x=0;$x<$i;$x++){
                $data['recordId'] = $value['guid'];
                $data['addtime'] = strtotime("2019-02-03 +".mt_rand(1,30)."days +2 hours");
                $this->insert($data);
            }
        }
        echo "success";
    }
}