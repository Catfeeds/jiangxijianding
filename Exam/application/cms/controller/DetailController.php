<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/26
 * Time: 9:02
 */
namespace app\cms\controller;




use app\common\controller\AdminBase;
use app\common\model\CmsArticle as MArticle;
use app\common\model\CmsFuServe as MFuServe;
use app\common\model\CmsGuide as MCmsGuide;
use app\common\model\CmsServe as MServe;
use app\common\service\CmsContact as MCmsContact;
use app\common\service\CmsOrder as MOrder;
use app\common\service\CmsPicture as MPicture;
use think\Controller;
use think\Exception;
use think\Request;
use think\Db;

class DetailController extends AdminBase
{
    protected $SGuide;
    protected $SContact;
    protected $SArticle;
    protected $SPicture;
    protected $SOrder;
    protected $Serve;
    protected $SFuserve;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SGuide = new MCmsGuide();
        $this->SContact = new MCmsContact();
        $this->SArticle =  new MArticle();
        $this->SPicture = new MPicture();
        $this->SOrder = new MOrder();
        $this->Serve = new MServe();
        $this->SFuserve = new MFuServe();

    }

    /**
     * 查看动态首页
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        //导航
        $data = $this->SContact->where('status',1)->where('order','<>',0)->order('order asc')->field('id,title,relation')->select();
        foreach ($data as $v)
        {
            $v['relation'] = $this->SGuide->where('pid',$v['id'])->field('id,guide_name')->select();
        }

        $info = $this->SGuide->column('id,guide_name');
        $this->assign('data',$data);
        $this->assign('info',$info);
        //轮播图
        $picture = $this->SPicture->zhanshi();
        $this->assign('picture',$picture);
        //顶部
        $top = $this->SOrder->topgui();
        $topinfo = $this->SOrder->topinfo($top);
        //$aaa = $this->SOrder->toparticle('','');

        $this->assign('top',$top);$this->assign('topinfo',$topinfo);
        //中部
        $section = $this->SOrder->sectiongui();
        $secinfo = $this->SOrder->sectioninfo($section);

        $this->assign('section',$section); $this->assign('secinfo',$secinfo);
       // dump($section);dump($secinfo);die;
        //底部
       $bottom = $this->SOrder->bottomgui();
        $botinfo = $this->SOrder->bottominfo($bottom);

        $this->assign('bottom',$bottom);$this->assign('botinfo',$botinfo);
        //网上服务
        $service = $this->Serve->getServeData();
        $serinfo[] = $this->SFuserve->getFser('s.order=1 AND f.order>0');
        $serinfo[] = $this->SFuserve->getFser('s.order=2 AND f.order>0');
        $serinfo[] = $this->SFuserve->getFser('s.order=3 AND f.order>0');
        $serinfo[] = $this->SFuserve->getFser('s.order=4 AND f.order>0');
        $this->assign('service',$service);$this->assign('serinfo',$serinfo);

         return view('');

    }
    public function ind()
    {
        return view('detail/ind');
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
     * 替换首页
     * @param Request $request
     * @return array
     */
    public function repla(Request $request)
    {
        if($request->isPost())
        {
           $asd = $this->buildHtml('shouye',APP_PATH.'/cms/view/keep/',APP_PATH.'/cms/view/index/index.html');
           $ad = $this->buildHtml('liedaohang',APP_PATH.'/cms/view/keep/',APP_PATH.'/cms/view/content/daohang.html');
            if(empty($asd))
            {
                return layuiMsg(0,'刷新失败');
            }else
                {
                    $this->index();
                  $ddd = $this->buildHtml('index',APP_PATH.'/cms/view/index/',APP_PATH.'/cms/view/detail/index.html');
                    if(empty($ddd))
                    {
                        return layuiMsg(0,'刷新失败');
                    }else
                        {
                            return layuiMsg(1,'刷新成功');
                        }
                }
        }
    }

    /**
     * 还原首页
     * @return array
     */
    public function restore()
    {
        $aaa = $this->buildHtml('index',APP_PATH.'/cms/view/index/',APP_PATH.'/cms/view/keep/shouye.html');
//        $bbb = $this->buildHtml('daohang',"./html/article/",APP_PATH.'/cms/view/keep/daohang.html');
//        $bbb = $this->buildHtml('daohang',APP_PATH.'/cms/view/content/',APP_PATH.'/cms/view/keep/daohang.html');
        if(empty($aaa))
        {
            return layuiMsg(0,'还原失败');
        }else
            {
                return layuiMsg('1','还原成功');
            }
    }


}