<?php
namespace app\common\model;

class LearningCollection extends BaseModel
{
    public function getFileTypeAttr($status)
    {
        switch ($status)
        {
            case 1:
                return '视频格式';
                break;
            case 2:
                return 'Docx文档格式';
                break;
            case 3:
                return 'PDF文件格式';
                break;
            case 4:
                return 'PPT文件格式';
                break;
            case 5:
                return 'Flash动画文件';
                break;
            default:
                return '未知';
                break;
        }
    }
}