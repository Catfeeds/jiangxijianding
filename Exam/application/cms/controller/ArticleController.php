<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/18
 * Time: 11:22
 */
namespace app\cms\controller;

use app\common\controller\AdminBase;
use app\common\model\CmsArticle as MArticle;

use app\common\model\CmsFu as MFu;
use app\common\model\CmsGuide as MGuide;

use app\common\service\CmsPicture as MPicture;
use app\common\service\ExamPlan;
use app\common\service\ExamType;
use app\common\service\WorkType;
use think\Db;
use think\Request;
use think\Controller;
use think\Session;


class ArticleController extends AdminBase
{
    protected $SArticle;
    protected $SFu;
    protected $SGuide;

    protected $SPicture;
    protected $SExamPlan;
    protected $SExamType;
    protected $SWorkType;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SArticle = new MArticle();
        $this->SFu = new MFu();
        $this->SGuide = new MGuide();

        $this->SPicture = new MPicture();
        $this->SExamPlan = new ExamPlan();
        $this->SExamType = new ExamType();
        $this->SWorkType = new WorkType();
    }
    public function index(Request $request)
    {
     $id = $request->param('guide_id');

     $data = $request->get();
     $guideInfo =$this->SGuide->BaseSelect(['id'=>['not in',[12,37,25,35,36,26]]]);

        $where = [];
        $time = '';$title='';
        $guide ='';
        if(!empty($data['time'])){
            $time = $data['time'];
            $data['time'] = explode('~',$data['time']);
            $data['time'][0] = strtotime($data['time'][0]);
            $data['time'][1] = strtotime($data['time'][1])+86400;
            $where['a.time'] = ['between',[$data['time'][0],$data['time'][1]]];
        }
        if(!empty($data['title']))
        {
            $title = $data['title'];
            $where['a.title'] = ['like','%'.$data['title'].'%'];
        }
        if(!empty($data['guide']))
        {
            $guide = $data['guide'];
            $where['a.guide_id'] = $data['guide'];
        }else{
            $where['a.guide_id'] = ['not in',[12,37,26]];
        }
        if(!empty($data['id']))
        {
            $id = $data['id'];
        }
      if( $id==12||$id==37)
      {
          $where['a.guide_id'] = $id;

          $data = $this->SArticle->getGuideDate($where);
          $info = $id;
//          dump($where);
//         dump($data);die;
          return view('',['data'=>$data,'info'=>$info,'time'=>$time,'title'=>$title,'guideInfo'=>$guideInfo]);

      }



      $data = $this->SArticle->getGuideDate($where);
//        dump($guideInfo);die;
      return view('',['data'=>$data,'time'=>$time,'title'=>$title,'guideInfo'=>$guideInfo,'guide'=>$guide]);
    }

    /**
     * 添加文章
     * @param Request $request
     * @return \think\response\View
     */
    public function add(Request $request)
    {

        $type = $request->param('type');
        if($type==1)
        {
            $data['id'] = 26;
            $data['guide_name'] = '图片文章';
            return view('pictureadd',['data'=>$data]);
        }
        $guide_id = $request->param('id');

        if(!empty($guide_id))
        {
            $exam_play = $this->SExamType->BaseSelect();
            $data = $this->SGuide->where('id',$guide_id)->field('guide_name,id')->find();
            $work_type = $this->SWorkType->BaseSelect();
            return view('add_jian',['data'=>$data,'exam_type'=>$exam_play,'work_type'=>$work_type]);
        }
        $data = $this->SGuide->where('id','<>',26)->where('id','<>',12)->where('id','<>',37)
            ->where('id','<>',29)->where('id','<>',35)->where('id','<>',36)->field('id,guide_name')->select();
        return view('',['data'=>$data]);
    }


    /**
     * 文章删除
     *
     * @return mixed
     */
    public function delete(Request $request)
    {

    }

    /**
     * 文章更新
     * @param Request $request
     * @param MArticle $article
     * @return \think\response\View
     */
    public function update(Request $request)
    {

        $id = $request->param('id');
        $type = $this->SArticle->where('id',$id)->field('type_picture,guide_id')->find();
        $article = $this->SArticle
            ->alias('a')
            ->join('__CMS_FU__ f','a.id=f.article_id','left')
            ->where('a.id',$id)->field('a.id,a.title,a.status,a.guide_id,a.source,a.time,a.urgency,a.type_exam_id,a.exam_type,a.work_type,a.red,f.content')->find();

        if($type['type_picture']==1)
        {
            $picture = $this->SPicture->where('article_id',$id)->field('id,url,type,sort')->find()->getData();
            $pictures['type']=$picture['type'];
            $pictures['url']=$picture['url'];
            $pictures['sort']=$picture['sort'];
            $pictures['id']=$picture['id'];
            $info['id'] = 26;
            $info['guide_name'] = '图片新闻';
            return view('pictureupdate',['data'=>$article,'picture'=>$pictures,'info'=>$info]);
        }
        if(in_array($type['guide_id'],[12,37]))
        {
            $exam_play = $this->SExamType->BaseSelect();
            $infos = $this->SExamPlan->column('id,title');
           $info = $this->SGuide->where('id',$type['guide_id'])->field('id,guide_name')->find();
            $work_type = $this->SWorkType->BaseSelect();
           return view('jian_update',['data'=>$article->getData(),'info'=>$info,'infos'=>$infos,'exam_type'=>$exam_play,'work_type'=>$work_type]);
        }
        $info = $this->SGuide->where('id','<>',25)->field('id,guide_name')->select();

        return view('',['data'=>$article,'info'=>$info]);

    }

    /**
     * 文章详情内容修改
     * @param Request $request
     * @return \think\response\View
     */
    public function content(Request $request)
    {
        $id = $request->param('id');
        $data = $this->SFu->where('article_id',$id)->find();
        return view('',['data'=>$data]);

    }

    /**
     * 添加附件
     * @param Request $request
     * @return array
     */
   public function fujian(Request $request)
   {
       $file= $request->file('file');
//       $article_id = $request->param('id');

       if(empty($file))
       {
           return layuiMsg(-1,'未上传文件');
       }

       $temp = explode('.',$_FILES['file']['name']);
       $extension = end($temp);
       $filename = array_shift($temp);
       if(!in_array($extension,array('pdf','docx','xlsx','doc')))
       {
           return layuiMsg(0,'上传文件不合法');
       }

    //  $dir =  DS.'uploads'.DS.'cms'.DS.'fujian'.DS;
       $info = $file->rule('uniqid')->move(ROOT_PATH,'public'.DS.'uploads/cms/fujian/'.rand(100000,999999));

       $fujian = str_replace('\\','/',$info->getSaveName());
      $fujian = strstr($fujian,'/');
       $arr['url'] = $fujian;
       $arr['name'] = $filename;
       $arr['create_time'] = time();
       if(!empty($article_id))
       {
           $arr['article_id'] = $article_id;
           $status = $this->SArticle->where('id',$arr['article_id'])->field('fujian')->find();
           if($status['fujian']==-0)
           {
               $res = $this->SArticle->where('id',$arr['article_id'])->update(['fujian'=>1]);

               if($res)
               {
                   $id = $this->SFujian->BaseSave($arr);

                   if($id)
                   {
                       return layuiMsg(1,'上传成功');
                   }else
                   {
                       return layuiMsg(0,'上传失败');
                   }
               }else
               {
                   return layuiMsg(0,'上传失败');
               }
           }else
               {
                   $id = $this->SFujian->BaseSave($arr);
                   if($id)
                   {
                       return layuiMsg(1,'上传成功',$id);
                   }else
                   {
                       return layuiMsg(0,'上传失败');
                   }
               }

       }else
           {
               $id = $this->SFujian->BaseSave($arr);
               if($id)
               {
                   return layuiMsg(1,'上传成功',$id);
               }else
                   {
                       return layuiMsg(0,'上传失败');
                   }
           }

   }

    /**
     * 修改文章中展示该文章附件
     * @param Request $request
     * @return \think\response\View
     */
      public function FujianIndex(Request $request)
      {

          $article_id = $request->param('id');

          $title = $this->SArticle->where('id',$article_id)->field('title')->find();
          $data = $this->SFujian->fujian($article_id);

          if(empty($data))
          {
              $data[$article_id]['title'] = $title['title'];
              $data[$article_id]['name'] = '无附件，请添加';
              $data[$article_id]['create_time'] = time();
              $data[$article_id]['article_id'] = $article_id;
              $data[$article_id]['id'] = 0;
          }
          return view('',['data'=>$data]);
      }

    /**
     * 新添加附件
     * @param Request $request
     * @return \think\response\View
     */
      public function Fujianadd(Request $request)
      {
          $article_id = $request->param('article_id');

          return view('',['article_id'=>$article_id]);
      }

    /**
     * 附件删除
     * @param Request $request
     * @return array
     */
     public function Fujiandelete(Request $request)
     {
         $id = $request->param('id');

         $indo = $this->SFujian->where('id',$id)->field('url,article_id')->find();


         $res = $this->SFujian->destroy(['id'=>$id]);
         if($res)
         {
             $info = $this->SFujian->where('article_id',$indo['article_id'])->select();
             unlink(ROOT_PATH.'public'.$indo['url']);
             if(empty($info))
             {
                $rea = $this->SArticle->where('id',$indo['article_id'])->update(['fujian'=>0]);
                 if($rea)
                 {
                       return layuiMsg(1,'删除成功');
                 }else
                       {
                           return layuiMsg(0,'删除失败1');
                       }
             }else
                 {

                     return layuiMsg(1,'删除成功');
             }

         }else
             {
                 return layuiMsg(0,'删除失败2');
             }
     }

}