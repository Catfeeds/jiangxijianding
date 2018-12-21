<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/16
 * Time: 17:54
 */
namespace app\cms\controller;

use app\common\controller\AdminBase;
use app\common\model\CmsArticle as MArticle;
use app\common\model\CmsFuServe as MFuServe;
use app\common\model\CmsFuServe;
use app\common\model\CmsGuide as MGuide;
use app\common\model\CmsPicture as MPicture;
use app\common\model\CmsPresentation;
use app\common\model\CmsServe as MServe;
use app\common\model\CmsServe;
use app\common\service\CmsContact as MContact;
use app\common\service\CmsOrder as MOrder;
use app\common\service\ExamType;
use app\common\service\WorkType;
use think\Controller;
use think\Request;
use think\Validate;

class ChangeController extends AdminBase
{
    protected $SGuide;
    protected $SPicture;
    protected $SArticle;
    protected $SOrder;
    protected $SServe;
    protected $SFuServe;
    protected $SContact;
    protected $SPresentation;
    protected $SExamType;
    protected $SWorkType;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SGuide = new MGuide();
        $this->SPicture = new MPicture();
        $this->SArticle = new MArticle();
        $this->SOrder = new MOrder();
        $this->SServe = new MServe();
        $this->SFuServe = new MFuServe();
        $this->SContact = new MContact();
        $this->SPresentation = new CmsPresentation();
        $this->SWorkType = new WorkType();
        $this->SExamType = new ExamType();
    }



    /**
     * 预览轮播图
     * @return \think\response\View
     */
    public function picture()
    {

        $data = $this->SPicture->where('status',1)->where('type',1)->where('sort','<>',0)->field('id,title,url,inter')->order(['id'=>'asc'])->select();

        return view('',['data'=>$data]);

    }

    /***预览导航页并生成静态
     * @return mixed
     */
    public function set($dir='./html/article/',Request $request)
    {
        $id = $request->param('id');
        $data = $this->SContact->where('status',1)->where('order','<>',0)->order('order asc')->field('id,title,relation')->select();
        foreach ($data as $v)
        {
            $v['relation'] = $this->SGuide->where('pid',$v['id'])->field('id,guide_name')->select();
        }

        $info = $this->SGuide->column('id,guide_name');
        $this->assign('data',$data);
        $this->assign('info',$info);
        if($id==0)
        {
            return  view('set/new_file');
        }else
            {
                if(!is_dir($dir))
                {
                    mkdir($dir,0755,true);
                }
               $this->buildHtml('daohang',APP_PATH.'cms/view/keep/','./html/article/daohang.html');
                $this->buildHtml('daohang',APP_PATH.'cms/view/keep/',APP_PATH.'cms/view/content/daohang.html');
                $this->buildHtml('daohang',APP_PATH.'cms/view/content/',APP_PATH.'cms/view/set/new_file.html');
               // $this->buildHtml('daohang',APP_PATH.'common/view/content/',APP_PATH.'cms/view/set/new_file.html');
                  $this->buildHtml('daohang',$dir,APP_PATH.'cms/view/set/new_file.html');
                  return layuiMsg(1,'成功生成');
            }


    }

    /***
     * 生成文章详情页
     * @param Request $request
     * @return mixed
     */
    public function article(Request $request)
    {
        $id = $request->param('id');

        $where['id'] = $id;
        $guide= $this->SArticle->where('id',$id)->field('guide_id')->find();
        $guip = $this->SGuide->alias('g')
            ->join('__CMS_CONTACT__ c','g.pid=c.id')
            ->where('c.delete_time','null')
            ->where('g.id',$guide['guide_id'])
            ->field('c.title')->find();

        $articles = $this->SArticle->where('guide_id',$guide['guide_id'])
            ->where('id','<>',$id)
            ->order('id desc')
            ->field('title,url')
            ->limit(10)->select();
         $exam_type = $this->SExamType->column('id,name');
         $work_type = $this->SWorkType->column('id,name');
        $data = $this->SArticle->getArticlefind($id);
            if($data['exam_type']!=0)
            {
                $data['exam_type'] = $exam_type[$data['exam_type']];
                $data['work_type']!=0? $work_type[$data['work_type']]:'';
            }
            $contact =  $guip['title']?$guip['title']:$data['guide_name'];

//        $info = $this->SArticle->getFujian($id);
//        dump($guip['title']);dump($data);dump($info);dump($articles);die;
//        echo "<pre>";
//        print_r($data);die;
        $this->assign('contact',$contact);
        $this->assign('data',$data);
//        $this->assign('info',$info);
        $this->assign('articles',$articles);
        $dir = './html/article/';
        if(!is_dir($dir))
        {
            mkdir($dir,0755,true);
        }
        $fielname = $id;
        $path = $dir;
        if($guide['guide_id']==12)
        {
            $presen1 = $this->SPresentation->where('type',1)->field('content')->find();
            $presen2 = $this->SPresentation->where('type',2)->field('content')->find();
            $presen3 = $this->SPresentation->where('type',3)->field('content')->find();
            $this->assign('presen1',$presen1);
            $this->assign('presen2',$presen2);
            $this->assign('presen3',$presen3);
            $rea = $this->buildHtml($fielname,$path,APP_PATH.'cms/view/change/article4.html');
        }else
            {
               $rea = $this->buildHtml($fielname,$path,APP_PATH.'cms/view/change/article.html');
            }

        $ress = $this->SPicture->where('article_id',$id)->field('id')->find();
        if($rea)
        {
           $info['urgency'] = 1;
            $info['url'] =substr($dir,1).$fielname.'.html';
            $picture['inter'] = $info['url'];
            if(!empty($ress)){ $this->SPicture->where('id',$ress['id'])->update($picture);}
            $res = $this->SArticle->BaseUpdate($info,$where);
            if($res)
            {

                return layuiMsg(1,'发布成功');
            }else
                {
                    return layuiMsg(0,'发布失败');
                }
        }
    }
    /***
     * @param string $htmlfile 生成的文件名
     * @param string $htmlpath  静态文件保存路径
     * @param string $templateFile 模板文件
     * @return mixed
     */
    public function buildHtml($htmlfile='',$htmlpath='',$templateFile='')
    {

        $content = $this->fetch($templateFile);
        $htmlpath = !empty($htmlpath)?$htmlpath:"./html/";
        $htmlfile = $htmlpath.$htmlfile.'.'.config('url_html_suffix');
        $File = new \think\template\driver\File();
        $File->write($htmlfile,$content);
        return $content;
    }


    /**
     * 更改首页顶部模块
     * @param Request $request
     * @return \think\response\View
     */
    public function top(Request $request)
    {
        $info = $this->SGuide->field('relation')->select();
        foreach($info as $v)
        {
            $arr = explode('-',$v['relation']);
            foreach ($arr as $v)
            {
                $arr2[] = $v;
            }
        }
        $where['id']=array_unique($arr2);

        $data = $this->SOrder->alias('o')->join('__CMS_GUIDE__ g','o.id=g.id')
            ->where('g.guide_test',0)
            ->where(['g.id'=>['not in',$where['id']]])
            ->where('g.delete_time','null')
            ->where('g.id','not in',[12,26,37,29,35,36])
            ->where(['g.site'=>['not in',[2,3]]])
            ->field('o.id,g.guide_name,o.guide_order,o.top,o.top_order,o.top_limit')->select();
        $datas = $this->SOrder->topgui();
        foreach ($datas as $v)
        {
            $infos[$v['id']]['id'] = $v['id'];
            $infos[$v['id']]['article'] = $this->SOrder->toparticle($v['id'],$v['top_limit']);
        }
        $this->assign('datas',$datas);
        $this->assign('info',$infos);
       // return  view('change/top');

        return view('',['data'=>$data]);
    }



    /**
     * 更改中部模板
     */
    public function section(Request $request)
    {
        $info = $this->SGuide->field('relation')->select();
        foreach($info as $v)
        {
            $arr = explode('-',$v['relation']);
            foreach ($arr as $v)
            {
                $arr2[] = $v;
            }
        }
        $where['id']=array_unique($arr2);

        $data = $this->SOrder->alias('o')->join('__CMS_GUIDE__ g','o.id=g.id')
            ->where('g.guide_test',0)
            ->where('g.delete_time','null')
            ->where('g.id','not in',[12,26,37,29,35,36])
            ->where(['g.id'=>['not in',$where['id']]])
            ->where(['g.site'=>['not in',[1,3]]])
            ->field('o.id,g.guide_name,o.guide_order,o.section,o.section_order,o.section_limit')->select();
        $datas = $this->SOrder->sectiongui();

        $infos = $this->SOrder->sectioninfo($datas);

        return view('',['data'=>$data,'datas'=>$datas,'info'=>$infos]);

    }



    /**
     * 更改底部
     * @param Request $request
     * @return \think\response\View
     * @user 王忠
     */
    public function bottom(Request $request)
    {

        $info = $this->SGuide->field('relation')->select();
        foreach($info as $v)
        {
            $arr = explode('-',$v['relation']);
            foreach ($arr as $v)
            {
                $arr2[] = $v;
            }
        }
        $where['id']=array_unique($arr2);

        $data = $this->SOrder->alias('o')->join('__CMS_GUIDE__ g','o.id=g.id')
                    ->where('g.guide_test',0)
            ->where('g.delete_time','null')
            ->where('g.id','not in',[12,26,37,29,35,36])
                    ->where(['g.id'=>['not in',$where['id']]])
                    ->where(['g.site'=>['not in',[1,2]]])
                    ->field('o.id,g.guide_name,o.guide_order,o.bottom,o.bottom_order,o.bottom_limit')->select();
        $datas = $this->SOrder->bottomgui();
        foreach ($datas as $v)
        {
            $infos[$v['id']]['id'] = $v['id'];
            $infos[$v['id']]['article'] = $this->SOrder->bottomarticle($v['id'],$v['bottom_limit']);
        }
        return view('',['data'=>$data,'datas'=>$datas,'info'=>$infos]);

    }


    /**
     * 网上服务栏目
     * @return \think\response\View
     */
    public function serve(Request $request)
    {
        $data = $this->SServe->paginate(10);
        return view('',['data'=>$data]);

    }

    /**
     * 网上服务栏目的添加
     * @param Request $request
     * @return \think\response\View
     */
    public function addServe(Request $request)
    {
        $data = $this->SFuServe->where(['serve_id'=>0])->field('name,id,serve_id')->select();
        return view('',['data'=>$data]);
    }


    /**
     * 网上服务栏目的修改
     * @param Request $request
     * @param CmsServe $serve
     * @return \think\response\View
     */
    public function serveUpdate(Request $request)
    {
        $id = $request->param('id');
        $info = $this->SServe->where('id',$id)->find();
            $data = $this->SFuServe->field('name,id,serve_id')->select();
           // $infos = $this->SFuServe->select();
        return view('',['info'=>$info,'data'=>$data]);
    }

    /**
     * 功能展示
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function fuServe()
    {
        $data = $this->SFuServe->order('id desc')->paginate(8);
        $info = $this->SServe->column('id,title');
//        echo "<pre>";
//        print_r($data);print_r($info);die;
        return view('',['data'=>$data,'info'=>$info]);
    }

    /**
     * 添加功能
     * @param Request $request
     * @return \think\response\View
     */
    public function addFuServe(Request $request)
    {
        $data = $this->SServe->getServe();
        return view('',['data'=>$data]);
    }



    /**
     * 功能的修改
     * @param Request $request
     * @param CmsFuServe $fuServe
     * @return \think\response\View
     */
    public function fuSerUpdate(Request $request,CmsFuServe $fuServe)
    {
        $data = $this->SServe->getServe();
        return view('',['info'=>$fuServe,'data'=>$data]);
    }

    /**
     * 预览网上服务导航页
     *
     */
    public function changeSer1()
    {
        $data = $this->SServe->where('order','>',0)->order('order')->select();
        return view('',['data'=>$data]);
    }

    /**
     * 更改功能位置
     * @param Request $request
     * @return \think\response\View
     */
    public function changeFuSer(Request $request)
    {
        if($request->isGet())
        {
            $data = $this->SServe->where('order','>',0)->order('order')->select();
            $info1 = $this->SFuServe->getFser(['s.order'=>1]);
            $info2 = $this->SFuServe->getFser(['s.order'=>2]);
            $info3 = $this->SFuServe->getFser(['s.order'=>3]);
            $info4 = $this->SFuServe->getFser(['s.order'=>4]);

            return view('',['data'=>$data,'info1'=>$info1,'info2'=>$info2,'info3'=>$info3,'info4'=>$info4]);

        }
        $data = $request->post();
         if($data['data']=='chong')
         {
             $res = $this->SFuServe->BaseUpdate(['order'=>0],['serve_id'=>$data['serve']]);
             if($res){
                 return layuiMsg(1,'重置成功');
             }else{
                 return layuiMsg(0,'重置失败');
             }
         }
        $infos = $this->SFuServe->where('serve_id',$data['serve'])->where('id','not in',$data['data'])->select();
       // echo $this->SFuServe->getLastSql();
        return $infos;

    }

    /**
     * 预览网上服务
     */
    public function service()
    {
        $data = $this->SServe->getServeData();
        $info1 = $this->SFuServe->getFser('s.order=1 AND f.order>0');
        $info2 = $this->SFuServe->getFser('s.order=2 AND f.order>0');
        $info3 = $this->SFuServe->getFser('s.order=3 AND f.order>0');
        $info4 = $this->SFuServe->getFser('s.order=4 AND f.order>0');
        $infos [] = $info1;$infos [] = $info2;$infos [] = $info3;$infos [] = $info4;
        $this->assign('data',$data);$this->assign('infos',$infos);

        return view('');
    }

    /**
     * 一键发布
     * @param Request $request
     */
    public function fabu(Request $request)
    {
        $ids = $this->SArticle->where('guide_id','not in',[12,37,26])->where('urgency',0)->field('id')->limit(100)->select();

        foreach ($ids as $rowId)
        {

            $re = $this->SArticle->getArticlefind($rowId['id']);
            if(is_file($re['url']))
            {
                unlink($re['url']);

            }
            if($re['red']==1)
            {
                $re['title'] = '<span style="color:#F00">'.$re['title'].'</span>' ;
            }
            $html = '';
            $li='';

            $guide= $this->SArticle->where('id',$rowId['id'])->field('guide_id')->find();
            $guip = $this->SGuide->alias('g')
                ->join('__CMS_CONTACT__ c','g.pid=c.id')
                ->where('c.delete_time','null')
                ->where('g.id',$guide['guide_id'])
                ->field('c.title')->find();

            $contact =  $guip['title']?$guip['title']:$re['guide_name'];
            $articles = $this->SArticle->where('guide_id',$guide['guide_id'])
                ->where('id','<>',$rowId['id'])
                ->order('id desc')
                ->field('title,url')
                ->limit(10)->select();
               $i = 1;
            foreach ($articles as $aa)
            {

                $li .= "<li><a target='_blank' href=".$aa['url']."><span>".$i."</span>".$aa['title']."</a></li>";

                 $i++;
            }


            $article = ['title' => $re['title'],'guide_id'=> $guide['guide_id'],'guide_name' => $re['guide_name'], 'source' => $re['source'],'content'=>$re['content'],'time'=>$re['time'],'contact'=>$contact];

            $dir = './html/article/';
            if (!is_dir($dir)) mkdir($dir, 0755);
            $fileName = $dir.$rowId['id'] . '.html';
            $data['url'] = substr($fileName,1);
            $data['urgency'] = 1;
            $articleContent = file_get_contents(APP_PATH . 'cms/view/change/article2.html');
         //   $daohang = file_get_contents(APP_PATH.'cms/view/content/daohang.html');
          //  $articleContent = str_replace("{daohang}",$daohang,$articleContent);
            $articleContent = str_replace("{title}",$article['title'],$articleContent);
            $articleContent = str_replace("{guide_name}",$article['guide_name'],$articleContent);
            $articleContent = str_replace("{time}",$article['time'],$articleContent);
            $articleContent = str_replace("{guide_id}",$article['guide_id'],$articleContent);
            $articleContent = str_replace("{contact}",$article['contact'],$articleContent);
            $articleContent = str_replace("{source}",$article['source'],$articleContent);
            $articleContent = str_replace("{content}",$article['content'],$articleContent);
            $articleContent = str_replace("{html}",$html,$articleContent);
            $articleContent = str_replace("{li}",$li,$articleContent);
            $resource = fopen($fileName, 'w');
            file_put_contents($fileName, $articleContent);
            $this->SArticle->where('id',$rowId['id'])->update($data);
        }
            return layuiMsg('1','发布成功');

    }


    public function select(Request $request)
    {
       $ids = $request->post();
        $ids = json_decode($ids['data']);

        $data = $this->SContact->where('id','not in',$ids)->field('id,title')->select();
        if($data)
        {
            return layuiMsg(1,'',$data);
        }else
            {
                return layuiMsg(0);
            }
    }

    /**
     * 添加文章预览
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function articleYu(Request $request)
    {

        $shoudata = $this->SContact->where('status',1)->where('order','<>',0)->order('order asc')->field('id,title,relation')->select();
        foreach ($shoudata as $v)
        {
            $v['relation'] = $this->SGuide->where('pid',$v['id'])->field('id,guide_name')->select();
        }

        $infos = $this->SGuide->column('id,guide_name');

        $this->assign('shoudata',$shoudata);
        //$this->assign('info',$infos);
        $id = $request->param('id');
        $where['id'] = $id;
        $guide= $this->SArticle->where('id',$id)->field('guide_id')->find();
        $guip = $this->SGuide->alias('g')
            ->join('__CMS_CONTACT__ c','g.pid=c.id')
            ->where('c.delete_time','null')
            ->where('g.id',$guide['guide_id'])
            ->field('c.title')->find();
        $articles = $this->SArticle->where('guide_id',$guide['guide_id'])
            ->where('id','<>',$id)
            ->order('id desc')
            ->field('title,url')
            ->limit(10)->select();
        $data = $this->SArticle->getArticlefind($id);


        $exam_type = $this->SExamType->column('id,name');
        $work_type = $this->SWorkType->column('id,name');
        if($data['exam_type']!=0)
        {
            $data['exam_type'] = $exam_type[$data['exam_type']];
            $data['work_type']!=0? $work_type[$data['work_type']]:'';
        }
        if($guide['guide_id']==12) {
            $presen1 = $this->SPresentation->where('type', 1)->field('content')->find();
            $presen2 = $this->SPresentation->where('type', 2)->field('content')->find();
            $presen3 = $this->SPresentation->where('type', 3)->field('content')->find();

            return view('change/article4', ['id' => $id, 'data' => $data,  'presen1' => $presen1,'presen2' => $presen2,
                'presen3' => $presen3]);
        }

        return view('change/article1',['data'=>$data,'contact'=>$guip['title'],'articles'=>$articles,]);
    }

    /**
     * 网上报名右侧静态页
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wenti(Request $request,$data='')
    {
        $presen1 = $this->SPresentation->where('type',1)->field('content')->find();
        $presen2 = $this->SPresentation->where('type',2)->field('content')->find();
        $presen3 = $this->SPresentation->where('type',3)->field('content')->find();
        $this->assign('presen1',$presen1);
        $this->assign('presen2',$presen2);
        $this->assign('presen3',$presen3);
        if(empty($data))
        {
            return view('');
        }
      $res =   $this->buildHtml('wenti','./html/article/',APP_PATH.'cms/view/change/wenti.html');
      if($res)
      {
          return layuiMsg(1,'更改成功');
      }else{
          return layuiMsg(0,'更改失败');
      }
    }
    public function daohang()
    {
        return view('content/daohang');
    }

}