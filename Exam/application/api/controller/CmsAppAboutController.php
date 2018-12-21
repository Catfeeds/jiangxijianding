<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/24
 * Time: 11:09
 */
namespace app\api\controller;

use app\common\controller\BaseApi;
use think\Request;

class CmsAppAboutController extends BaseApi
{
    protected $SAppAbout;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SAppAbout = new \app\common\service\CmsAppAbout();
    }
    public function add(Request $request)
    {
        $data = $request->post();

        if($data['pid']==1 && $data['type']==0)
        {
            $res = $this->SAppAbout->BaseSave($data);
        }else{
            $res = $this->SAppAbout->BaseUpdate($data,['pid'=>0,'type'=>1]);
            if($res)
            {
                return layuiMsg(1,'更改成功');
            }else{
                return layuiMsg(0,'更改失败');
            }
        }
        if($res)
       {
           return layuiMsg(1,'添加成功');
       }
       return layuiMsg(0,'添加失败');
    }

    public function update(Request $request)
    {
        $data =$request->post() ;
        $data['type'] =(bool)$data['type'];
        $data['status']=(bool)$data['status'];
        $res = $this->SAppAbout->update($data);
        if($res)
        {
            return layuiMsg(1,'更改成功');
        }
        return layuiMsg(0,'更改失败');
    }
    public function delete(Request $request)
    {
        $id['id'] = $request->param('id');
        $res = $this->SAppAbout->BaseDelete($id);
        if($res)
        {
            return layuiMsg(1,'删除成功');
        }
        return layuiMsg(0,'删除失败');

    }
}