<?php
namespace app\index\controller;
use think\Controller;

use app\index\model\RecordModel;
use app\index\model\RecordLogModel;

class Record extends Controller
{
    public function index(){
        return $this->fetch();
    }

    public function getRecordList($FileGUID)
    {
        $logModel = new RecordLogModel();
        $recordModel = new RecordModel();
        $list = $recordModel->getListByFileId($FileGUID);

        //插入查询记录信息
        $data = [];
        $data['addtime'] = time();
        foreach ($list as $value){
            $data['recordId'] = $value->toArray()['guid'];
            $logModel->insert($data);
        }

        //格式化处理结果集
        $list = $this->changeNullTo($list);
        $list =  $this->changeIntToString($list);

        $result['status'] = 1;
        $result['msg'] = "成功";
        $result['data'] = $list;

        return json_encode($result);
    }

    public function saveRecordList($GUID=null,
                                   $FileGUID=null,
                                   $EmbedTime=null,
                                   $SourceNodeGUID=null,
                                   $DestNodeGUID=null,
                                   $UserGUID=null,
                                   $WatermarkIndex=null,
                                   $WatermarkContent=null,
                                   $Remark=null)
    {
        if ($GUID==null){
            $result['status'] = 0;
            $result['msg'] = "GUID参数传入失败";
            $result['data'] = [];
        }elseif ($FileGUID==null){
            $result['status'] = 0;
            $result['msg'] = "FileGUID参数传入失败";
            $result['data'] = [];
        } elseif ($EmbedTime==null){
            $result['status'] = 0;
            $result['msg'] = "EmbedTime参数传入失败";
            $result['data'] = [];
        }elseif ($SourceNodeGUID==null){
            $result['status'] = 0;
            $result['msg'] = "SourceNodeGUID参数传入失败";
            $result['data'] = [];
        }elseif ($DestNodeGUID==null){
            $result['status'] = 0;
            $result['msg'] = "DestNodeGUID参数传入失败";
            $result['data'] = [];
        }elseif ($UserGUID==null){
            $result['status'] = 0;
            $result['msg'] = "UserGUID参数传入失败";
            $result['data'] = [];
        }elseif ($WatermarkIndex==null){
            $result['status'] = 0;
            $result['msg'] = "WatermarkIndex参数传入失败";
            $result['data'] = [];
        }elseif ($WatermarkContent==null){
            $result['status'] = 0;
            $result['msg'] = "WatermarkContent参数传入失败";
            $result['data'] = [];
        }elseif ($Remark==null){
            $result['status'] = 0;
            $result['msg'] = "Remark参数传入失败";
            $result['data'] = [];
        }else{
            $recordModel = new RecordModel();

            $data = [];
            $data['guid'] = $GUID;
            $data['fileGuid'] = $FileGUID;
            $data['embedTime'] = $EmbedTime;
            $data['sourceNodeGuid'] = $SourceNodeGUID;
            $data['destNodeGuid'] = $DestNodeGUID;
            $data['userGuid'] = $UserGUID;
            $data['watermarkIndex'] = $WatermarkIndex;
            $data['watermarkContent'] = $WatermarkContent;
            $data['remark'] = $Remark;
            $data['addtime'] = time();

            $status = $recordModel->saveList($data);
            $result = [];
            if ($status !== false){
                $result['status'] = 1;
                $result['msg'] = "水印嵌入成功";
                $result['data'] = [];
            }else{
                $result['status'] = 0;
                $result['msg'] = $recordModel->getError();
                $result['data'] = [];
            }
        }

        return json_encode($result);
    }

    public function changeNullTo(&$data) {
        if (is_array($data)) {
            foreach ($data as &$val) {
                if (is_array($val) || is_object($val)) {
                    $val = is_object($val)?$val->toArray():$val;
                    $this->changeNullTo($val);
                } else {
                    if (is_null($val)) {
                        $val = "";
                    }
                }
            }
        } else {
            if($data == null) {
                $data = "";
            }
        }
        return $data;
    }

    public function changeIntToString(&$data) {
        if (is_array($data)) {
            foreach ($data as &$val) {
                if (is_array($val)) {
                    $this->changeIntToString($val);
                } else {
                    if(is_int($val)) {
                        $val = strval($val);
                    }
                }
            }
        } else {
            if(is_int($data)) {
                $data = strval($data);
            }
        }
        return $data;
    }

    function unicodeDecode($unicode_str){
        $json = '{"str":"'.$unicode_str.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }
}
