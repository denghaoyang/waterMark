<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22 0022
 * Time: 下午 7:30
 */
namespace app\index\model;

use think\Model;
use think\Db;

class NodeModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'wt_node';

    public function getList(){
        return $this->where("is_del",0)->paginate(10);
    }

    public function saveList($data){
        return $this->allowField(true)->validate(
            [
                'guid'  => 'unique:wt_node'
            ],
            [
                'guid.unique' => 'guid重复'
            ]
        )->save($data);
    }

    public function findList($id){
        return $this->where("is_del",0)->find($id);
    }

    public function updateList($data){
        return $this->isUpdate(true)->save($data);
    }
}