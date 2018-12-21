<?php
namespace app\common\model;

class WorkType extends BaseModel
{


    /**
     * @param $status
     * @return string
     * @user 李海江 2018/9/27~5:07 PM
     */
    public function getStatusAttr($status)
    {
        switch ($status) {
            case 1:
                return '<b style="color: green;">启用</b>';
                break;
            case 0:
                return '<b style="color: red;">禁用</b>';
                break;
            default:
                return '未知';
                break;
        }
    }

}