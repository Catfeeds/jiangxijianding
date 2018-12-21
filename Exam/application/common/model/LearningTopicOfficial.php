<?php
namespace app\common\model;

class LearningTopicOfficial extends BaseModel
{
    /***
     * @param $status
     * @return string
     * @user 刘欣 2018/10/30~10:06
     */
    public function getTypeAttr($status)
    {
        switch ($status)
        {
            case 1:
                return '单选题';
                break;
            case 2:
                return '多选题';
                break;
            case 3:
                return '判断题';
                break;
            case 4:
                return '填空题';
                break;
            case 5:
                return '简答题';
                break;
            case 6:
                return '作文题';
                break;
            case 7:
                return '论述题';
                break;
            case 8:
                return '分析题';
                break;
            case 9:
                return '操作题';
                break;
            default:
                return '未知';
                break;
        }
    }

    /***
     * @param $status
     * @return string
     * @user 刘欣 2018/10/30~10:06
     */
    public function getRangeAttr($status)
    {
        switch ($status)
        {
            case 1:
                return '正规题库';
                break;
            case 2:
                return '作业题库';
                break;
            case 3:
                return '模拟题库';
                break;
            default:
                return '未知';
                break;
        }
    }

    /***
     * @param $status
     * @return string
     * @user 刘欣 2018/10/30~10:15
     */
    public function getTopicLevelAttr($status)
    {
        switch ($status)
        {
            case 1:
                return '易';
                break;
            case 2:
                return '偏易';
                break;
            case 3:
                return '适中';
                break;
            case 4:
                return '偏难';
                break;
            case 5:
                return '难';
                break;
            default:
                return '未知';
                break;
        }
    }
}