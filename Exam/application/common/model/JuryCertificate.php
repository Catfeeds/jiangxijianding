<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/12/6
 * Time: 5:00 PM
 */

namespace app\common\model;


/**
 * Class JuryCertificate
 * @package app\common\model
 */
class JuryCertificate extends BaseModel
{

    /**
     * 模型关联 证书 - 工种
     * @return \think\model\relation\BelongsTo
     * @user 李海江 2018/12/6~9:45 PM
     */
    public function work()
    {
        return $this->belongsTo('work', 'work_id', 'id');
    }

    /**
     * @param $level
     * @return string
     * @user 李海江 2018/12/6~9:55 PM
     */
    public function getCardLevelAttr($level)
    {
        switch ($level) {
            case '00':
                return '考评员';
                break;
            case '01':
                return '高级考评员';
                break;
            default:
                return '未定义';
        }
    }

    /**
     * @param $time
     * @return false|string
     * @user 李海江 2018/12/6~9:55 PM
     */
    public function getCardCreateTimeAttr($time)
    {
        return date('Y年m月d日', $time);
    }

    /**
     * @param $time
     * @return false|string
     * @user 李海江 2018/12/6~9:55 PM
     */
    public function getCardExpireTimeAttr($time)
    {
        return date('Y年m月d日', $time);
    }
}