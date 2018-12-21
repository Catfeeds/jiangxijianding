<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/18
 * Time: 13:01
 */
namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\CmsGuide;
use app\common\service\CmsOrder;
use think\Request;

class CmsOrderController extends BaseApi
{
    protected $SOrder;
    protected $SGuide;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SOrder = new CmsOrder();
        $this->SGuide = new CmsGuide();
    }

    /**
     * 更改顶部模块
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function top(Request $request)
    {
        $infos = $request->post();
        $ids = [];
        foreach ($infos as $v)
        {
            $ids[] = $v['id'];
        }

        $this->SOrder->rere($ids,'top',0);
        $this->SOrder->rere($ids,'top_order',0);
        $this->SOrder->rere($ids,'top_limit',6);
        $this->SGuide->where('site',1)->update(['site'=>0]);
        $this->SGuide->where('id','in',$ids)->update(['site'=>1]);
        $res = $this->SOrder->allowField(true)->saveAll($infos);
        if($res)
        {
            return layuiMsg(1,'修改成功,点击刷新首页才会生效');
        }else
        {
            return layuiMsg(0,'修改失败');
        }
    }

    /**
     * 更改中部与底部模块
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function section(Request $request)
    {
        $data = $request->post();
        $ids = [];
       $seat = $data['seat'];
       unset($data['seat']);
       foreach ($data as $v)
        {
            $ids[]= $v['id'];
        }

        if($seat==='bottom')
        {
            $seat = 'bottom';
            $site = 3;
            $limit = 8;
        }else
            {
                $seat = 'section';
                $site = 2;
                $limit = 10;
            }

        $this->SOrder->rere($ids,$seat,0);
        $this->SOrder->rere($ids,$seat.'_order',0);
        $this->SOrder->rere($ids,$seat.'_limit',$limit);
        $this->SGuide->where('site',$site)->update(['site'=>0]);
        $this->SGuide->where('id','in',$ids)->update(['site'=>$site,]);
        $res = $this->SOrder->allowField(true)->saveAll($data);
        if($res)
        {
            return layuiMsg(1,'修改成功,点击刷新首页才会生效');
        }else
        {
            return layuiMsg(0,'修改成功');
        }
    }
}