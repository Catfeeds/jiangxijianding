<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/18
 * Time: 10:40
 */
namespace app\api\controller;


use app\common\controller\BaseApi;
use app\common\service\CmsPicture;
use think\Request;

class CmsPictureController extends BaseApi
{
    protected $SPicture;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SPicture = new CmsPicture();
    }

    /**
     * 轮播图状态修改
     * @param Request $request
     * @return array
     */
    public function picture(Request $request)
    {
        $data = $request->post();
        $where['id'] = $request->post('id');
        $data['status'] ==0?$data['status']=0:$data['status']=1;
        if($data['status']==0)
        {
            $data['sort'] =0;
        }
        if($data['status']==1)
        {
            $num = $this->SPicture->where('status',1)->count('status');
            if($num==6)
            {
                return layuiMsg(0,'最多只能启用6个');
            }
        }
        $res = $this->SPicture->BaseUpdate($data,$where);
        if($res)
        {
            return layuiMsg(1,'操作成功');
        }else
        {

            return layuiMsg(0,'操作失败');
        }
    }

    /**
     * 图片新闻的删除
     * @param Request $request
     * @return array
     */
    public function picdelete(Request $request)
    {
        $id = $request->param('id');
        $data = $this->SPicture->where('id',$id)->field('article_id')->find();
        $res = $this->SPicture->destroy($id);
        if($res)
        {

            return layuiMsg(1,'删除成功');
        }else
        {
            return layuiMsg(0,'删除失败');
        }
    }

    /**
     * 上传图片
     * @param Request $request
     * @return array
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        if(empty($file))
        {
            return layuiMsg(2,'未上传图片');
        }

        $upDir = DS.'uploads'.DS.'cms'.DS.'picture'.DS;
//        if(!is_dir($upDir))
//        {
//            mkdir($upDir,0755,true);
//        }
        $info = $file->validate(['ext'=>'jpg,png,jpeg，gif'])->move(ROOT_PATH.'public'.$upDir);

        if($info)
        {

            $res['code'] = 1;
            $res['msg'] = '上传成功';
            $res['index'] =str_replace('\\','/',$upDir.$info->getSaveName()) ;
            return $res;
        }else
        {
            return layuiMsg(0,'上传失败,文件格式不正确【jpg,png,jpeg,gif】');
        }
    }

    public function sort(Request $request)
    {

       $arr['id'] = $request->param('id');
       $arr['sort'] = $request->param('sort');
       $arr['type'] = $request->param('type');
//        dump($request->post());die;
       $info = $this->SPicture->BaseFind(['id'=>$arr['id']],['status']);
        if($info['status']==0)
        {
            return layuiMsg(0,'禁用下不能修改播放顺序');
        }
       if($arr['sort']!=0)
       {
           $infos = $this->SPicture->BaseSelect(['sort'=>$arr['sort'],'type'=>$arr['type']]);
           if(!empty($infos))
           {
               return layuiMsg(0,'播放顺序重复，请重新选择');
           }
       }

       $res = $this->SPicture->update($arr);
       if($res)
       {
           return layuiMsg(1,'更改成功');
       }else{
           return layuiMsg(0,'更改失败');
       }
    }
}