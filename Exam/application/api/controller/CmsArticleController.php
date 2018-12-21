<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/18
 * Time: 13:45
 */
namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\CmsArticle;
use app\common\service\CmsFu;
use app\common\service\CmsPicture;
use think\Db;
use think\Request;

class CmsArticleController extends BaseApi
{
    protected $SArticle;
    protected $SFu;
    protected $SPicture;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SArticle = new CmsArticle();
        $this->SFu = new CmsFu();
        $this->SPicture = new CmsPicture();
    }

    /**
     * 添加文章
     * @param Request $request
     */
    public function add(Request $request)
    {
        $data = $request->post();
       $validate = Validate('Checking');
       if(!$validate->scene('article')->check($data))
       {
           return layuiMsg(0,$validate->getError());
       }
        $data['time'] = strtotime($data['time']);
        $cot['content'] = $data['content'];
//        dump($data);die;
        if(!empty($data['switch']))
        {
            Db::startTrans();
            try{
                $picture['type'] = $data['type'];
                $picture['url'] = $data['picture_url'];
                $picture['title'] = $data['title'];
                $data['type_picture'] = 1;
                $rs = $this->SArticle->allowField(true)->BaseSave($data);
                $cot['article_id'] = $rs;
                $picture['article_id'] = $rs;
                $res = $this->SFu->save($cot);
                $pictures =  $this->SPicture->BaseSave($picture);
                if($rs && $res && $pictures)
                {

                    Db::commit();
                    return layuiMsg(1,'添加成功,启用后才能发布该文章');
                }
            }catch (\Exception $e)
            {
                Db::rollback();
                return layuiMsg(0,'添加失败');
            }
        }
        Db::startTrans();
        try{
            $rs = $this->SArticle->allowField(true)->BaseSave($data);
            $cot['article_id'] = $rs;
            $res = $this->SFu->save($cot);
            if($rs && $res)
            {
                Db::commit();
                return layuiMsg(1,'添加成功');
            }
        }catch (\Exception $e)
        {
            Db::rollback();
            return layuiMsg(0,'添加失败');
        }
    }

    /**
     * 修改文章
     * @param Request $request
     * @return array
     */
    public function update(Request $request)
    {
        $data = $request->post();
        $content['article_id'] = $data['id'];
        $content['content'] = $data['content'];
        unset($data['content']);
        $validate = Validate('Checking');
        if(!$validate->scene('article')->check($data))
        {
            return layuiMsg(0,$validate->getError());
        }
        $data['time'] = strtotime($data['time']);
        $data['url'] = '';
        $data['urgency'] = 0;

        if(!empty($data['switch']))
        {
            $picture['type'] = $data['type'];

            $picture['url'] = $data['picture_url'];
            $picture['title'] = $data['title'];
           // $this->SPicture->where('id',$data['picture_id'])->update($picture);
            unset($data['switch']);
            $picture['title'] = $data['title'];
            $where['id'] = $data['picture_id'];

            $this->SPicture->BaseUpdate($picture,$where);
            unset($data['sort'],$data['picture_id'],$data['picture_url'],$data['type'],$data['file'],$data['url']);
        }

        $where['id'] = $data['id'];
        $res = $this->SArticle->BaseUpdate($data,$where);
        if($res)
        {
           $ress =  $this->content($content);
           if($ress)
           {
               return layuiMsg(1,'修改成功');
           }else
               {
                   return layuiMsg(0,'修改失败');
               }

        }else
        {
            return layuiMsg(0,'修改失败');
        }
    }

    /**
     * 修改文章详情
     * @param Request $request
     * @return mixed
     */
    public function content($data)
    {
        $where['article_id'] = $data['article_id'];
        $res =$this->SFu->BaseUpdate($data,$where);
          return $res;
    }

    /**
     * 文章删除
     * @param Request $request
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete(Request $request)
    {
        $id['id'] = $request->post('id');

        $fu_id = $this->SFu->where('article_id',$id['id'])->field('id')->find();

        $data= $this->SArticle->where('id',$id['id'])->field('url,urgency')->find();
        $infos = $this->SPicture->where('article_id',$id['id'])->field('id')->find();
        if(!empty($infos))
        {
            $this->SPicture->destroy(['id'=>$infos['id']]);
        }

        $res = $this->SArticle->destroy($id);

        $ress = $this->SFu->destroy(['id'=>$fu_id['id']]);

        if($ress && $res)
        {
            if($data['urgency'] ==1)
            {
                file_exists('.'.$data['url'])?unlink('.'.$data['url']):'';
            }

            return layuiMsg(1,'删除成功');
        }
        return layuiMsg(0,'删除失败');
    }
}