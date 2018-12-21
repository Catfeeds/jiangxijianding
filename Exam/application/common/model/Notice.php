<?php
/**
 * Created by PhpStorm.
 * User: lihaijiang
 * Date: 2018/11/2
 * Time: 9:25 AM
 */

namespace app\common\model;


class Notice extends BaseModel
{
    public function getCreateTimeAttr($time)
    {
        return date('Y年m月d日', $time);
    }
}