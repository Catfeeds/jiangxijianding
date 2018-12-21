<?php
/**
 * Created by PhpStorm.
 * User: WQ
 * Date: 2018/10/08
 * Time: 12:03
 */

namespace app\cms\controller;


use app\common\model\CmsGuide as MGuide;
use app\common\model\CmsPhone;
use app\common\model\CmsPresentation;
use app\common\service\Certificate;
use app\common\service\CmsArticle as MArticle;
use app\common\service\CmsContact as MContact;
use app\common\service\CmsOrder as MOrder;
use app\common\service\CmsPicture as MPicture;
use app\common\service\Organize;
use app\common\service\OrganizeWork;
use app\common\service\Work;
use app\common\service\WorkDirection;
use app\common\service\WorkType;
use think\Config;
use think\Controller;
use think\Request;

class IndexController extends Controller
{
    protected $SGuide;
    protected $SPicture;
    protected $SOrder;
    protected $SContact;
    protected $SArticle;
    protected $SPhone;
    protected $SPresentation;
    protected $SOrganize;
    protected $SorganizeModel;
    protected $SWork;
    protected $SWorkDirection;
    protected $SWorkType;
    protected $SCertificate;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SGuide = new MGuide();
        $this->SPicture = new MPicture();
        $this->SOrder = new MOrder();
        $this->SContact =new MContact();
        $this->SArticle = new MArticle();
        $this->SPhone = new CmsPhone();
        $this->SPresentation = new CmsPresentation();
        $this->SOrganize = new Organize();
        $this->SorganizeModel = new OrganizeWork();
        $this->SWork = new Work();
        $this->SWorkDirection = new WorkDirection();
        $this->SWorkType = new WorkType();
        $this -> SCertificate = new Certificate();

    }
    public function xml(){

        $item = xml_read(config::get('xml.trials_no_pass'));
        dump($item);

        $student= [['id'=>1,'name'=>'张三','sex'=>'男'],['id'=>1,'name'=>'李四','sex'=>'女'],['id'=>1,'name'=>'王五','sex'=>'其他']];
        xml_write(config::get('xml')['student'],$student);

        $item = xml_read(config::get('xml')['student']);
        dump($item);
    }

    public function index()
    {

        $picture = $this->SPicture->zhanshi();
        $top = $this->SOrder->topgui();


        return view('',['picture'=>$picture,'top'=>$top]);
    }

    /**
     * 栏目列表页
     * @param Request $request
     * @return \think\response\View
     */
    public function category(Request $request){
        if($request->isGet())
        {
            $id = $request->param('id');
            $contact = $this->SGuide->where('id',$id)->field('id,guide_contact,guide_name,relation')->find();
            $infos['id'] = $id;
            $infos['count'] = $this->SGuide->alias('g')
                ->join('__CMS_ARTICLE__ a','g.id = a.guide_id')
                ->where('a.delete_time','null')
                ->where('g.id',$id)
                ->where('a.urgency',1)
                ->count();
            $data = $this->SGuide->alias('g')
                ->join('__CMS_CONTACT__ c','g.pid = c.id')
                ->where('c.delete_time','null')
                ->where('g.id',$id)->field('g.pid,g.guide_name,g.id,g.guide_contact,g.relation,c.title')->find();

            if(!empty($data))
            {

                $info = $this->SGuide->where('pid',$data['pid'])->where('id','<>',$id)->field('id,guide_name')->select();
                   return view('index/category1',['data'=>$data,'infos'=>$infos,'info'=>$info]);
            }else
                {
                    if($contact['guide_contact']==1)
                    {
                        $contact['relation'] = explode('-',$contact['relation']);

                        $info = $this->SGuide->column('id,guide_name');
                        return view('index/category',['data'=>$contact,'info'=>$info,'infos'=>$infos]);
                    }

                    return view('index/category2',['data'=>$contact,'infos'=>$infos,]);

                }


        }else {
            $id = $request->param('id');
            $pag = $request->param('pag');
            $limit = $request->param('limit');

            $info = $this->SGuide->getArticle($id,$limit, $pag);

            return $info;

        }
    }

    /**
     *
     * @param Request $request
     * @return \think\response\View
     */
    public function login(Request $request)
    {

         return view('user/login');

    }

    public function about(Request $request){
      $id = $request->param('id');
      $infos['id'] = $id;
     $data = $request->post();
     $where = [];

     if(!empty($data['exam_type']))
       {

           $where['a.exam_type'] = $data['exam_type'];
           $where['a.work_type'] = $data['work_type'];
           $infos['plan'] = $data['exam_type'];
           $infos['work'] = $data['work_type'];

       }

       $infos['count'] = $this->SGuide->alias('g')
                        ->join('__CMS_ARTICLE__ a','g.id = a.guide_id')
                        ->where('g.id',$id)
                        ->where($where)
                        ->where('a.urgency',1)
                      ->where('a.delete_time','null')
                        ->count();

          return $infos;

    }

    /**
     * 搜索
     * @param Request $request
     * @return false|int|\PDOStatement|string|\think\Collection|\think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function seek(Request $request)
    {
        if($request->isGet())
        {
            $data = $request->get();

            $infos = $this->SArticle->where('urgency',1)->where('title','like','%'.$data['sousuo'].'%')->field('title,url,')->count();
            return view('',['infos'=>$infos,'data'=>$data['sousuo']]);
        }else
            {
                $data = $request->post();

                $pag = $request->param('pag');
                $limit = $request->param('limit');
                $infos = $this->SArticle->where('urgency',1)->where('title','like','%'.$data['data'].'%')->field('title,url,time')->limit($limit)
                    ->page($pag)->select();
                return $infos;
            }
    }

    /**
     * 中心介绍
     */
    public function centre(Request $request)
    {
        $id = $request->param('id');
        $datas = $this->SGuide->where('pid',$id)->select();
        $infos = '';
        $infoss ='';
        if($id!=11)
        {
            $datas = '';
            $infos = $this->SGuide->where('id',$id)->field('id,guide_name,pid')->find();

            $infoss = $this->SGuide->where('id','<>',$id)->where('pid',$infos['pid'])->field('id,guide_name')->select();
        }


        $data = $this->SPresentation->where('type',4)->find();
        $info = $this->SPresentation->find();
        return  view('',['datas'=>$datas,'data'=>$data,'info'=>$info,'id'=>$id,'infos'=>$infos,'infoss'=>$infoss]);
    }

//    /**
//     * 找回密码
//     * @return \think\response\View
//     */
//    public function forgetpass()
//    {
//        return view('user/forget_pass');
//    }
//
//    /**
//     *
//     * @return \think\response\View
//     */
//    public function forgetpasstwo()
//    {
//        $mobile = input('get.mobile');
//        return view('user/forget_passtwo',['mobile'=>$mobile]);
//    }
//
//    public function forgetpassthree()
//    {
//        return view('user/forget_passthree');
//    }

    /**
     * 社会考生登陆
     * @return \think\response\View
     */
    public function studenlogin()
    {
        return view('examinee/login');
    }

    /**
     * 社会考生登陆注册
     */
      public function signature()
      {
          return view('examinee/signature');
      }
     public function sign()
     {
         $signature = $this->request->param();

         if(empty($signature)){
             return $this->redirect('/cms/index/signature');
         }else{
             return view('/examinee/sign',['signature' => $signature['name']]);
         }
     }
    /**
     * 社会考生登找回密码
     * @return \think\response\View
     */
      public function forgetpass()
      {
          return view('examinee/forgetpass');
      }
      public function forgetpasstwo()
      {
          $mobile = input('get.mobile');
          return view('examinee/forgetpasstwo',['mobile'=>$mobile]);
      }
      public function forgetpassthree()
      {
          return view('examinee/forgetpassthree');
      }

    /**
     * 成绩查询
     * @return \think\response\View
     */
      public function indexgrade()
      {
          return view('examinee/indexgrade');
      }

    /**
     * 准考证打印
     * @return \think\response\View
     */
      public function indexticket()
      {
          return view('examinee/indexticket');
      }

    /**
     * 证书查询
     * @return \think\response\View
     */
      public function certificate()
      {
          return view('examinee/certificate');
      }

    /**
     * 证书查询结果
     */
     public function certinquire()
     {
         $data = session('user');
         foreach( $data as $k=>$v ) {
             if( !$v ){
                 unset( $data[$k] );
             }
         }
         $data = $this -> SCertificate -> BaseSelect($data);
         return view('examinee/certinquire',['datas' =>$data]);
     }
    /**
     * 鉴定公告与网上报名查询
     */
     public function jian(Request $request)
     {
         if($request->isGet())
         {
             $guide_id = $request->param('guide');
             $data = $this->SGuide->where('id',$guide_id)->field('id,guide_name')->find();
             $infos =$this->SWorkType->BaseSelect([],['id','name']);
             $infos = collection($infos)->toArray();
             foreach ($infos as $v)
             {
                 $v['type']= 1;
             }

             $info['id'] = $guide_id;
             $info['count'] = $this->SGuide->alias('g')
                 ->join('__CMS_ARTICLE__ a','g.id = a.guide_id')
                 ->where('g.id',$guide_id)
                 ->where('a.delete_time','null')
                 ->where('a.urgency',1)
                 ->count();
             return view('',['data'=>$data,'infos'=>$infos,'info'=>$info]);
         }else
             {

                $id = $request->param('id');
                 $plan = $request->param('plan');
                 $work = $request->param('work');
                 $pag = $request->param('pag');
                 $limit = $request->param('limit');
                 $infos = $this->SArticle->where('guide_id',$id)->where('urgency',1)->where('delete_time','null')->where(['exam_type'=>$plan,'work_type'=>$work])->field('title,url,time')->limit($limit)
                     ->page($pag)->select();

                 return $infos;
             }

     }

     public function indexcondition()
     {
         $map = ['status' => 1];
         $list = $this->SWork->getAlls($map, ['id', 'code', 'name']);
         return view('examinee/indexcondition',['list'=>$list]);
     }
    /**
     * 机构查询
     * @return \think\response\View
     */
     public function work()
     {
         $map = [];
         //分页参数
//         $paginate = array(
//             config('paginate.list_rows'),
//             false,
//             ['query' => request()->param()]
//         );
         $arrData['exam.id'] =1;
         $arrData['work'] = '';
         $arrData['WorkDirection'] = '';
         $arrData['Level'] = '';
         $arrData['sou'] = '';
         if (Request::instance()->isPost() || Request::instance()->isGet()) {
             $arrData = Request::instance()->param();
             //dump($arrData);die;
           //  dump($arrData);die;

             if (!empty($arrData['work'])) {
                 $map['work.id'] = $arrData['work'];
             } else {
                 $arrData['work'] = '';
             }
             if (!empty($arrData['WorkDirection'])) {
                 $map['work_direction.id'] = $arrData['WorkDirection'];
             } else {
                 $arrData['WorkDirection'] = '';
             }
             if (!empty($arrData['Level'])) {
                 $map['exam_work_level.work_level'] = $arrData['Level'];
             } else {
                 $arrData['Level'] = '';
             }
             if(!empty($arrData['sou']))
             {
                 $map['organize.name'] = ['like','%'.$arrData['sou'].'%'];
             }else
                 {
                     $arrData['sou'] = '';
                 }
         }
         $field = "organize.id,organize.type,organize.name,organize.address,organize.build_date,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
         $arrOrganize = $this->SorganizeModel->getListData($map,$field,"organize.update_time desc","organize.id");//,$paginate);
         $arrWork = $this->SWork->BaseSelect("",['name','id']);
         $arrWorkDirection = $this->SWorkDirection->BaseSelect("",['name','id']);
         $arrLevel = [1,2,3,4,5];

         return view('',['arrOrganize'=>$arrOrganize,'map'=>$arrData,'arrWork'=>$arrWork,"arrWorkDirection"=>$arrWorkDirection,"arrLevel"=>$arrLevel]);
     }

    public function details()
    {
        if (Request::instance()->isGet()){
            $arrData = Request::instance()->param();
            if (empty($arrData['id']) || empty($arrData['type'])){
                /** @var TYPE_NAME $this */
                return $this->success("非法操作","/admin/ExamPlan/index");
            }
            if ($arrData['type'] == 1){
                $arrData['type'] = 6;
            }else if ($arrData['type']== 2){
                $arrData['type'] = 7;
            }else if ($arrData['type'] == 3){
                $arrData['type'] = 4;
            }else{
                $arrData['type'] = 0;
            }
            $field = "organize.address,organize.build_date,work_type.`name` as typename,work_type.id as wtid,`work`.id as wid,`work`.`name` as workname,work_direction.`name` as wdname,exam_work_level.work_level";
            $arrWork = $this->getList($arrData,$field);
            return view("details",['arrWork'=>$arrWork]);
        }
    }

    public function getList($where = [],$field)
    {
        $map['organize.id'] = $where['id'];
        $map['exam_work.type'] = $where['type'];
        $map['exam_work_level.type'] = $where['type'];
        //查询鉴定计划所有的数据
        $arrOrganize = $this->SorganizeModel->getListData($map,$field);
        $arrOrganize = collection($arrOrganize)->toArray();

        foreach ($arrOrganize as $k=>$v){
            $v['address'] = str_replace(","," ",$v['address']);
            $arrOrganize[$k]['build_date'] = date("Y-m-d H:i:s",$v['build_date']);
        }

        //取出不同的东西
        $arr = array_columns($arrOrganize,['wid','workname','wdname','work_level']);
        //根据workid 去重
        $arrWork = array_unique_key($arrOrganize,"wid");

        //获取到work对应的方向名称 级别
        foreach ($arrWork as $k=>$v){
            $arrWork[$k]['wdname'] = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'wdname'));
            $work_level = array_unique(array_column(array_where($arr,['wid'=>$v['wid'],]),'work_level'));
            sort($work_level);
            $arrWork[$k]['level'] = $work_level;
        }
        if (empty($arrOrganize)){
            $arrWork = $this->SOrganize->BaseSelect(['id'=>$where['id']]);
            foreach ($arrWork as $k=>$v){
//                $v['address'] = str_replace(","," ",$v['address']);
                $arrWork[$k]['build_date'] = date("Y-m-d H:i:s",$v['build_date']);
                $arrWork[$k]['typename'] = '';
                $arrWork[$k]['wtid'] = '';
                $arrWork[$k]['wid'] = '';
                $arrWork[$k]['workname'] = '';
                $arrWork[$k]['work_level'] = '';
                $arrWork[$k]['wdname'] = [];
                $arrWork[$k]['level'] = [];

            }
        }

//        print_r($arrWork);die;
        return $arrWork;
    }
   public function lianxi()
   {
        return view('');
   }
    public function jump(Request $request)
    {

        $id = $request->param('id');
        $data= $this->SArticle->where(['type_exam_id'=>$id,'guide_id'=>37])->field('url')->find();

        //header("location:".$data['url']);
        $this->redirect($data['url']);
    }


    /**
     * 鉴定工作者登录
     * @return \think\response\View
     * @user 李海江 2018/12/7~11:06 AM
     */
    public function admin()
    {
        return view();
    }

}