<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/17
 * Time: 14:49
 */
namespace app\common\model;
use think\Request;

class Picture extends BaseModel
{
    public static function invoke(Request $request)
    {
        $id = $request->param('id');
        return self::field('id,title,url,order')->find($id);
    }
}