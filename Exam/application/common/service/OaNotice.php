<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/12/3
 * Time: 9:57
 */
namespace app\common\service;

class OaNotice extends \app\common\model\OaNotice
{
    public function getNotice($id,$where=['create_time',['between',[1543593600,1543593600]]])
    {
        return $this
            ->where('user_id',$id)
            ->where($where)
            ->order('id desc')
            ->paginate(config('paginate.list_rows'),false,['query'=>Request()->param()]);
    }
}