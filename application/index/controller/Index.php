<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\IndexModel;

class Index extends Controller
{
    public function index(){
        $this->view->engine->layout(true);
        return $this->fetch();
    }

    public function getRecordList()
    {
        $indexModel = new IndexModel();
        $list = $indexModel->getListByFileId(2);
        return json_encode($this->changeNullTo($list));
    }

    public function saveRecordList($GUID,
                                   $FileGUID,
                                   $EmbedTime,
                                   $SourceNodeGUID,
                                   $DestNodeGUID,
                                   $UserGUID,
                                   $WatermarkIndex,
                                   $WatermarkContent,
                                   $Remark)
    {
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

        $indexModel = new IndexModel();
        $list = $indexModel->saveList($data);
        return json_encode($this->changeNullTo($list));
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
}
