<?php
namespace app\common\model;

class LearningQuestion extends BaseModel
{
    public $level_id;
    public $type;
    public $range;

    public function __construct($data = [])
    {
        parent::__construct($data);
        if(count($data)>0) {
            if (key_exists('level_id', $data)) {
                if (!empty($data['level_id'])) {
                    $level_name = ['0'=>'无','1'=>'高级技师','2'=>'技师','3'=>'高级','4'=>'中级','5'=>'初级'];
                    $this->level_id = $level_name[$data['level_id']];
                }
            }

            if (key_exists('type', $data)) {
                if (!empty($data['type'])) {
                    $type = ['1'=>'单选题','2'=>'多选题','3'=>'判断题','4'=>'填空题','5'=>'简答题','6'=>'作文题','7'=>'论述题','8'=>'分析题','9'=>'操作题'];
                    $this->type = $type[$data['type']];
                }
                if (key_exists('range', $data)) {
                    if (!empty($data['range'])) {
                        $range = ['1' => '在线练习', '2' => '模拟考试'];
                        $this->range = $range[$data['range']];
                    }
                }
            }
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