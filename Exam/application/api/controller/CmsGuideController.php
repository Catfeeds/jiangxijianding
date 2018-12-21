<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/18
 * Time: 10:07
 */
namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\model\CmsGuide;
use app\common\service\CmsContact;
use app\common\service\CmsOrder;
use think\Db;
use think\Request;

class CmsGuideController extends BaseApi
{
    protected $SGuide;
    protected $SPicture;
    protected $SOrder;
    protected $SContact;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SGuide = new CmsGuide();
        $this->SOrder = new CmsOrder();
        $this->SContact = new CmsContact();
    }

    /**
     * 栏目添加
     * @param Request $request
     * @return array
     */
    public function addColumn(Request $request)
    {
        $data = $request->post();
        //$result = $this->validate($data["guide_name"],'Checking.column');

        $validate = Validate('Checking');
        if(!$validate->scene('column')->check($data))
        {
            return layuiMsg(0,$validate->getError());
        }
        if(empty($data['guide_test']))
        {
            $data['guide_test'] = 0;
        }else
        {
            $data['guide_test'] =1;
        }
        if(empty($data['relation']))
        {
            $data['guide_contact'] = 0;
        }else
        {
            $data['guide_contact'] = 1;
            $this->SGuide->where('id','in',$data['relation'])->update(['guide_contact'=>1]);
            $data['relation'] = rtrim(implode('-',$data['relation']),'-');
        }
        $data['create_time'] = time();
        Db::startTrans();
        try{
            $res = $this->SGuide->BaseSave($data);
            $order['id'] = $res;
            $ress =$this->SOrder->BaseSave($order);
            if($ress && $res)
            {
                Db::commit();
                return layuiMsg(1,'添加成功');
            }else
            {
                return layuiMsg(0,'添加失败');
            }
        }catch (\Exception $e)
        {
            Db::rollback();
        }
    }

    /**
     * 栏目删除
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        Db::startTrans();
        try{
            $id= $request->post('id');
            $res = $this->SGuide->destroy($id);
            $ress = $this->SOrder->destroy($id);
            if($res & $ress)
            {
                Db::commit();
                return layuiMsg(1,'删除成功');
            }
        }catch (\Exception $e)
        {
            Db::rollback();
            return layuiMsg(0,'删除失败');

        }
    }
    public function update(Request $request)
    {
        $data = $request->post();

        $validate = Validate('Checking');
        if(!$validate->scene('column')->check($data))
        {
            return layuiMsg(0,$validate->getError());
        }
        if(empty($data['guide_test']))
        {
            $data['guide_test'] = 0;
        }else
        {
            $data['guide_test'] =1;
        }
        if(empty($data['relation']))
        {
            $data['guide_contact'] = 0;
        }else
        {
            $data['guide_contact'] = 1;
            $this->SGuide->where('id','in',$data['relation'])->update(['guide_contact'=>1]);
            $data['relation'] = rtrim(implode('-',$data['relation']),'-');
        }
        $res = $this->SGuide->update($data);
        if($res)
        {
            return layuiMsg(1,'修改成功');
        }
        else
        {
            return layuiMsg(0,'修改失败');
        }
    }
}