<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/18
 * Time: 12:05
 */
namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\model\CmsFuServe;
use app\common\model\CmsServe;
use think\Db;
use think\Request;

class CmsServeController extends BaseApi
{
    protected $SServe;
    protected $SFuSer;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SServe = new CmsServe();
        $this->SFuSer = new CmsFuServe();
    }

    /**
     * 删除网上服务栏目
     * @param Request $request
     * @return array
     */
    public function serveDelete(Request $request)
    {
        $id = $request->param('id');
        $res = $this->SServe->destroy($id);
        if($res)
        {
            $this->SFuSer->BaseUpdate(['serve_id'=>0,'order'=>0],['serve_id'=>$id]);
            return layuiMsg(1,'删除成功');
        }else
        {
           return layuiMsg(0,'删除失败');
        }
    }

    /**
     * 修改
     * @param Request $request
     * @return array
     */
    public function serveUpdate(Request $request)
    {
        $data = $request->post();
        $validate = Validate('Checking');
        if(!$validate->scene('serve')->check($data))
        {
            return layuiMsg(0,$validate->getError());
        }
        $where['id'] = $request->post('id');
         $ress = $this->SFuSer->BaseUpdate(['serve_id'=>0],['serve_id'=>$where['id']]);
         $res = $this->SServe->BaseUpdate($data,$where);
        if($res)
        {
            if(!empty($data['fu_ser'])) {
                $i = 1;
                foreach ($data['fu_ser'] as $v) {

                    $this->SFuSer->where('id',$v)->update(['serve_id'=>$where['id'],'order'=>$i]);
                    $i++;
//                   $aaa[] = $where['id'];
                }
//               dump($aaa);die;
            }//$this->SFuSer->BaseUpdate(['serve_id'=>$where['id'],'order'=>0],['id'=>['in',$data['fu_ser']]]);

            return layuiMsg(1,'修改成功');
        }else
        {
            return layuiMsg(0,'修改失败');
        }
    }

    /**
     * 添加
     * @param Request $request
     * @return array
     */
    public function addServe(Request $request)
    {
        if($request->isPost())
        {
            $data = input('post.');
            $validate = Validate('Checking');
            if(!$validate->scene('serve')->check($data))
            {
                return layuiMsg(0,$validate->getError());
            }
//            $result = $this->validate($data['title'],'Checking.serve');
//            if (true !== $result) {
//                return layuiMsg(0,$result);
//            }
            $data['create_time'] = time();

            $res = $this->SServe->BaseSave($data);
            if($res)
            {
                if(!empty($data['fu_ser']))
                {
                    foreach ($data['fu_ser'] as $k => $v)
                    {
                        $k = $k+1;
                        $this->SFuSer->BaseUpdate(['serve_id'=>$res,'order'=>$k],['id'=>$v]);
                    }
                }

                return layuiMsg(1,'添加成功');
            }
            }else
            {
                return layuiMsg(0,'添加失败');
            }
    }

    /**
     * 状态修改
     * @param Request $request
     * @return array
     * @throws \think\Exception
     */
    public function serve(Request $request)
    {
        $data = $request->post();
        $where['id'] = $request->post('id');

        if($data['status']==1)
        {
            $num = $this->SServe->where('status',1)->count('status');
            if($num==4)
            {
                return layuiMsg(0,'只能启用4个');
            }
        }else
        {
            $data['order'] = 0;
        }
        $res = $this->SServe->BaseUpdate($data,$where);
        if($res)
        {
            return layuiMsg(1,'更改成功');
        }else
        {
            return layuiMsg(0,'更改失败');
        }
    }

    /**
     * 更改服务栏目顺序
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeSer(Request $request)
    {
        $arr['id'] = $request->param('id');
        $arr['order'] = $request->param('order');
        $info = $this->SServe->where('id',$arr['id'])->field('status')->find();
        if($arr['order']!=0)
        {
            $infos = $this->SServe->BaseSelect(['order'=>$arr['order']]);
            if(!empty($infos))
            {
                return layuiMsg(0,'排序重复，请重新选择');
            }
        }

        if($info['status']==0)
        {
            return layuiMsg(0,'禁用下不能选择排序');
        }
        $res = $this->SServe->update($arr);
        if($res)
        {
            return layuiMsg(1,'更改成功');
        }else {
            return layuiMsg(0,'更改失败');
        }

    }
}