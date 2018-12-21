<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/16
 * Time: 18:54
 */
namespace app\common\model;

use traits\model\SoftDelete;
use think\Request;

class Zan extends BaseModel
{
    use SoftDelete;
    public static function invoke(Request $request)
    {
        $id = $request->param('id');
        return self::field('id,zan_name,zan_comon,level')->find($id);
    }


}