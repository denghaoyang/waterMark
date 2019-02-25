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

class UserModel extends Model{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'wt_user';

    public function checkUsernameAndPwd($userName,$pwd){
        return $this->where(['username'=>$userName,'pwd'=>$pwd,'is_del'=>0])->find();
    }
}