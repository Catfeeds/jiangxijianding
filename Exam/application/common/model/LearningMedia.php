<?php
namespace app\common\model;

class LearningMedia extends BaseModel
{


    /***
     * @param $status
     * @return string
     * @user 刘欣 2018/10/30~9:58
     */
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