<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/24
 * Time: 8:55
 */
namespace app\common\model;

use think\Request;

class CmsFuServe extends BaseModel
{
    public static function invoke(Request $request)
    {
        $id = $request->param('id');
        return self::find($id);
    }
    public function getFser($where)
    {
        return $this->alias('f')
            ->join('__CMS_SERVE__ s','f.serve_id = s.id')
            ->where($where)
            ->where('s.delete_time','null')
            ->order('f.order')->field('f.id,f.name,f.order,f.log_url,f.url')->select();
    }
}