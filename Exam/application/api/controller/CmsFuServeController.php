<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/18
 * Time: 12:28
 */

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\model\CmsFuServe;
use think\Request;

class CmsFuServeController extends BaseApi
{
    protected $SFuServe;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SFuServe = new CmsFuServe();
    }

    /**
     * 删除功能
     * @param Request $request
     */
    public function fuServeDelete(Request $request)
    {
        $id = $request->param('id');
        $res = $this->SFuServe->destroy($id);
        if($res)
        {
            return layuiMsg(1,'删除成功');
        }else
        {
            return layuiMsg(0,'删除失败');
        }
    }

    /**
     * 添加功能
     * @param Request $request
     * @return array
     */
    public function addFuServe(Request $request)
    {
        $data = $request->post();
        $validate = Validate('Checking');
        if(!$validate->scene('fuserve')->check($data))
        {
            return layuiMsg(0,$validate->getError());
        }
        $data['create_time'] = time();
        $res = $this->SFuServe->BaseSave($data);
        if($res)
        {
            return layuiMsg(1,'添加成功');
        }else
        {
            return layuiMsg(0,'添加失败');
        }
    }

    /**
     * 功能修改
     * @param Request $request
     * @return array
     */
    public function fuSerUpdate(Request $request)
    {
        $data = $request->post();
        $validate = Validate('Checking');
        if(!$validate->scene('fuserve')->check($data))
        {
            return layuiMsg(0,$validate->getError());
        }
        if(empty($data['log_url'])) unset($data['log_url']);
        $where['id'] = $request->param('id');
        $res = $this->SFuServe->BaseUpdate($data,$where);
        if($res)
        {
            return layuiMsg(1,'修改成功');
        }else
        {
            return layuiMsg(0,'修改失败');
        }
    }

    /**
     * 更改功能位置
     * @param Request $request
     * @return mixed
     */
    public function changeFuSer(Request $request)
    {
        $shuju = $request->post();
        $ids = [];
        foreach ($shuju as $v)
        {
            $ids[] = $v['id'];
        }

        $this->SFuServe->where('id','not in',$ids)->setField('order',0);
        $res = $this->SFuServe->BaseSaveAll($shuju);
        if($res)
        {
            return layuiMsg(1,'更改成功,请点击刷新首页才会生效');
        }else
        {
            return layuiMsg(0,'更改失败');
        }
    }
}