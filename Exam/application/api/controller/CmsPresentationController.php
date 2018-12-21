<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/19
 * Time: 10:42
 */
namespace app\api\controller;

use app\common\controller\BaseApi;
use think\Request;

class CmsPresentationController extends BaseApi
{
    protected $SPresentation;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SPresentation = new \app\common\model\CmsPresentation();
    }
    public function add(Request $request)
    {
        $data = $request->post();
        $data['create_time'] = time();
        if(empty($data['id']))
        {
            $res = $this->SPresentation->save($data);
        }else
            {
                $res = $this->SPresentation->update($data);
            }
            if($res)
        {
            return layuiMsg(1,'更改成功');
        }
        return layuiMsg(0,'更改失败');
    }
}