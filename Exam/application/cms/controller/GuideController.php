<?php
/**
 * Created by PhpStorm.
 * User: 王忠
 * Date: 2018/10/16
 * Time: 9:14
 */
namespace app\cms\controller;

use app\common\controller\AdminBase;
use app\common\model\CmsGuide as MGuide;
use app\common\model\CmsOrder as MOrder;
use app\common\model\CmsPicture as MPicture;
use app\common\model\CmsPicture;
use app\common\service\CmsArticle;
use app\common\service\CmsContact as MContact;
use think\Controller;
use think\Db;
use think\Request;
use think\Validate;

class GuideController extends AdminBase
{
    protected $SGuide;
    protected $SPicture;
    protected $SOrder;
    protected $SContact;
    protected $SArticle;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SGuide = new MGuide();
        $this->SPicture = new MPicture();
        $this->SOrder = new MOrder();
        $this->SContact = new MContact();
        $this->SArticle = new CmsArticle();

    }

    public function index()
    {
        return view('');
    }

    /**
     * 栏目的展示
     * @param Request $request
     * @return \think\response\View
     */
    public function show(Request $request)
    {
        $data = $this->SGuide->where('id','<>',26)
            ->where('id','not in',[12,37,29,36,35])->order('id desc')
            ->paginate(10);

                return view('',['data'=>$data]);

    }

    /**
     * 栏目添加
     * @param Request $request
     * @return \think\response\View
     */
    public function addColumn(Request $request)
    {
        $data = $this->SGuide->where('guide_test',0)->where('id','not in',[12,26,37,29,35,36])->select();
        return view('',['data'=>$data]);

    }


    /**
     * 栏目的更新
     * @param Request $request
     *
     * @return \think\response\View
     */
    public function update(Request $request)
    {

                $where['id'] = $request->param('id');
                $data = $this->SGuide->where($where)->find();
                $info = $this->SGuide->where('id','<>',$where['id'])->where('id','not in',[12,26,37,29,35,36])->where('guide_test',0)->field('id,guide_name,relation')->select();
                $ids = explode('-',$data['relation']);

                if($data['guide_contact']==1)
                {
                    $this->SGuide->where('id','in',$ids)->update(['guide_contact'=>0]);
                }

                return view('',['data'=>$data,'info'=>$info,'ids'=>$ids]);

    }

    /**
     * 轮播图展示
     * @return \think\response\View
     */
    public function picture(Request $request)
    {
        $data = $request->get();
        $where = [];
        $time = '';$title='';
        if(!empty($data['time'])){
            $time = $data['time'];
            $data['time'] = explode('~',$data['time']);
            $data['time'][0] = strtotime($data['time'][0]);
            $data['time'][1] = strtotime($data['time'][1])+86400;
            $where['p.create_time'] = ['between',[$data['time'][0],$data['time'][1]]];
        }
        if(!empty($data['title']))
        {
            $title = $data['title'];
            $where['a.title'] = ['like','%'.$data['title'].'%'];
        }
        if(!empty($data['type']))
        {
            $where['a.type_picture'] = $data['type'];
        }
                $data = $this->SPicture->alias('p')
                    ->join('__CMS_ARTICLE__ a','p.article_id=a.id')
                    ->where('a.delete_time','null')
                    ->field('p.*,a.urgency')
                    ->where($where)
                    ->order('p.id desc')->paginate(6);

        return view('',['data'=>$data,'title'=>$title,'time'=>$time]);




    }




    /**
     * 轮播图入库
     */
   public function upadd(Request $request)
   {
       $data = $request->post();
       $data['create_time'] = time();
       $res = $this->SPicture->BaseSave($data);
       if($res)
       {
           $statusMsg['status'] = 1;
           $statusMsg['msg'] = '操作成功';
           return $statusMsg;
       }else
           {
               $statusMsg['status'] = -1;
               $statusMsg['msg']  = '操作失败';
               return $statusMsg;
           }
   }



    /**
     * 首页导航栏下的栏目
     * @return \think\response\View
     */
      public function secondary(Request $request)
      {
          $data = $this->SContact->where('id','<>',11)->field('id,title,order,status,create_time,update_time')->paginate(8);
          foreach($data as $v)
          {
              $v['relation'] =  $this->SGuide->guideCOntact($v['id']);
          }
//
          return view('',['data'=>$data]);


      }
    /**
     * 导航栏下展示栏目添加
     */
    public function add(Request $request)
    {
        $data = $this->SGuide->guideData();
        return view('',['data'=>$data]);

    }

    /**
     * 修改导航栏下展示栏目
     *
     */
    public function secupdate(Request $request)
    {
        $where['id'] = $request->param('id');
        $info = $this->SContact->where($where)->find();
        $data = $this->SGuide->where(['pid'=>['in',[0,$where['id']]]])->field('id,guide_name,pid')->select();
        return view('',['info'=>$info,'data'=>$data]);

    }


}