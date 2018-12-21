<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/19
 * Time: 11:32
 */
namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\model\CmsPhone;
use think\Request;

class CmsPhoneController extends BaseApi
{
    protected $SPhone;

    /**
     * 构造函数
     * CmsPhoneController constructor.
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SPhone = new CmsPhone();
    }

    /**
     * 添加
     * @param Request $request
     * @return array
     */
    public function add(Request $request)
    {
        $data = $request->post();
        if(empty($data['phone']))
        {
            $data['phone'] = '0719-88301905';
        }
        $result = $this->validate($data,'Checking.phone');
        if(true !== $result){return layuiMsg(0,$result);}
        if(!empty($data['id']))
        {
            $ress = $this->SPhone->update($data);
            if($ress)
            {
                return layuiMsg(1,'修改成功');
            }
            return layuiMsg(0,'修改失败');
        }
        $res = $this->SPhone->save($data);
        if($res)
        {
            return layuiMsg(1,'添加成功');
        }
        return layuiMsg(0,'添加失败');
    }
}