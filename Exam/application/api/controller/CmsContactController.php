<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/18
 * Time: 11:15
 */
namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\CmsContact;
use app\common\service\CmsGuide;
use think\Request;


class CmsContactController extends BaseApi
{
    protected $SContact;
    protected $SGuide;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SContact = new CmsContact();
        $this->SGuide = new CmsGuide();
    }

    /**
     * 更改导航栏顺序
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $arr['id'] = $request->param('id');
        $arr['order'] = $request->param('order');
        $Order = $this->SContact->BaseFind(['id'=>$arr['id']]);
        $info = $this->SContact->where('id',$arr['id'])->field('status')->find();
        $infos = $this->SContact->BaseFind(['order'=>$arr['order'],'status'=>1]);

        if($info['status']==0)
        {
            return layuiMsg(0,'禁用下不能修改顺序');
        }
        $this->SContact->BaseUpdate(['order'=>$Order['order']],['id'=>$infos['id']]);
        $res = $this->SContact->update($arr);
        if($res)
        {
            return layuiMsg(1,'更改成功');
        }else {
            return layuiMsg(0,'更改失败');
        }
    }

    /**
     * 导航栏状态更改
     * @param Request $request
     * @return array
     * @throws \think\Exception
     */
    public function secondary(Request $request)
    {
        $data = $request->post();
        $where['id'] = $request->post('id');
        if($data['status']==1)
        {
            $num = $this->SContact->where('status',1)->count('status');
            if($num==6)
            {
                return layuiMsg(0,'只能启用6个');
            }
        }else
        {
            $data['order'] = 0;
        }
        $res = $this->SContact->BaseUpdate($data,$where);
        if($res)
        {
            return layuiMsg(1,'更改成功');
        }else
        {
            return layuiMsg(0,'更改失败');
        }


    }
    /**
     * 导航栏删除
     * @param Request $request
     * @return array
     */
    public function secdelete(Request $request)
    {
        $id = $request->param('id');
//        $ids = $this->SContact->ids($id);
//        dump($ids);die;
        $res = $this->SContact->destroy($id);
        if($res)
        {
            $this->SGuide->where('pid',$id)->update(['pid'=>0]);
            return layuiMsg(1,'删除成功');
        }else
        {
            return layuiMsg(0,'删除失败');
        }
    }

    /***
     * 导航栏添加
     * @param Request $request
     * @return array
     */
    public function  add(Request $request)
    {
        $data = $request->post();
        if(empty($data['relation']))
        {
            return layuiMsg(0,'所属栏目必选');
        }
        $validate = Validate('Checking');
        if(!$validate->scene('contact')->check($data))
        {
            return layuiMsg(0,$validate->getError());
        }
        $data['status']=0;
        $ids = $data['relation'];
        unset($data['relation']);
        $data['create_time'] = time();
        $res = $this->SContact->BaseSave($data);
        if($res)
        {
            $ress = $this->SGuide->where('id','in',$ids)->update(['pid'=>$res]);
            if($ress)
            {
                return layuiMsg(1,'添加成功');
            }else
            {
                return layuiMsg(0,'添加失败');
            }

        }else
        {
            return layuiMsg(0,'添加失败');
        }
    }

    /**
     * 导航栏修改
     * @param Request $request
     * @return array
     */
    public function secupdate(Request $request)
    {
        $data = $request->post();
        $validate = Validate('Checking');
        if(!$validate->scene('contact')->check($data))
        {
            return layuiMsg(0,$validate->getError());
        }
        $pid['pid'] = $data['id'];
        $data['update_time'] = time();
        $this->SGuide->where('pid',$pid['pid'])->update(['pid'=>0]);
        if(empty($data['ids']))
        {
            $res = $this->SContact->update($data);
            if($res )
            {
                return layuiMsg(1,'修改成功');
            }else
            {
                return layuiMsg(0,'修改失败');
            }
        }
        $ids = $data['ids'];
        unset($data['ids']);
        $res = $this->SContact->update($data);
        $ra = $this->SGuide->where('pid',$pid['pid'])->update(['pid'=>0]);
        $ress =$this->SGuide->where('id','in',$ids)->update($pid);
        if($res && $ress)
        {
            return layuiMsg(1,'修改成功');
        }else
        {
            return layuiMsg(0,'修改失败');
        }

    }
}