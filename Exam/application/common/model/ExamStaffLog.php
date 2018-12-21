<?php
namespace app\common\model;

class ExamStaffLog extends BaseModel
{

    public function getTypeAttr($type)
    {
        switch ($type)
        {
            case 1:
                return '主考';
                break;
            case 2:
                return '流动监考';
                break;
            case 3:
                return '考评人员';
                break;
            case 4:
                return '督导员';
                break;
            case 5:
                return '论文评审';
                break;
            case 6:
                return '答辩专家';
                break;
            case 7:
                return '副主考';
                break;
            case 8:
                return '试卷接送人';
                break;
            default:
                return '未知';
                break;
        }
    }
}