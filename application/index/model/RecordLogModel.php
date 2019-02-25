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

    public function getList(){

    }
}