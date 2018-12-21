<?php
/**
 * Created by PhpStorm.
 * User: zhuying
 * Date: 2018/11/6
 * Time: 10:05
 */

namespace app\api\controller;

use app\common\service\StbArea;
use think\Request;
use app\common\controller\BaseApi;

class StbAreaController extends BaseApi
{
    private $SstbArea;
    /**
     * ExamEnrollFileController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SstbArea = new StbArea();
    }

    public function selCityBycode()
    {
        $webData = Request::instance()->param();
        if (!$webData){
            return layuiMsg('-1', '频繁操作');
        }
        $map['code'] = $webData['code'];
        $areaId = $this->SstbArea->BaseFind($map,"id");
        $cityMap['parent_id'] = $areaId->id;
        $arrCity = $this->SstbArea->BaseSelect($cityMap);
        return layuiMsg(1,"获取成功", $arrCity);
    }
}