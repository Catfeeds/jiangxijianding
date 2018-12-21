<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/11/27
 * Time: 19:33
 */
namespace app\cms\controller;

use app\common\controller\AdminBase;
use app\common\service\Admin;
use app\common\service\CmsNoticeFujian;
use app\common\service\ExamCenter;

use app\common\service\OaNotice;
use app\common\service\OaSend;
use app\common\service\Office;
use think\Request;

class NoticeController extends AdminBase
{

  protected $SAdmin;
  protected $SExam_center;
  protected $SOffice;
  protected $Oa_send;
  protected $Oa_notice;
  protected $SNotFujian;
  public function __construct(Request $request)
  {
      parent::__construct();

      $this->SAdmin = new Admin();
      $this->SExam_center = new ExamCenter();
      $this->SOffice = new Office();
      $this->Oa_send = new OaSend();
      $this->Oa_notice = new OaNotice();
      $this->SNotFujian = new CmsNoticeFujian();
  }
  public function index(Request $request)
  {
      $user= session('adminuser');
      $datas = $request->get();
      $time1 = '';$title1 = '';
      $time2 = '';$title2 ='';
     $where1= []; $where2=[];
     if(!empty($datas['status'])&& $datas['status']==1){
              if(!empty($datas['time'])){
                  $time1 = $datas['time'];
                  $datas['time'] = explode('~',$datas['time']);
                  $datas['time'][0] = strtotime($datas['time'][0]);
                  $datas['time'][1] = strtotime($datas['time'][1])+86400;
                  $where1['create_time'] = ['between',[$datas['time'][0],$datas['time'][1]]];

              }
              if(!empty($datas['title']))
              {
                  $title1 =$datas['title'];
                  $where1['title'] = ['like','%'.$datas['title'].'%'];
              }

          }elseif(!empty($datas['status'])&& $datas['status']==2){
              if(!empty($datas['time'])){
                  $time2 = $datas['time'];
                  $datas['time'] = explode('~',$datas['time']);
                  $datas['time'][0] = strtotime($datas['time'][0]);
                  $datas['time'][1] = strtotime($datas['time'][1])+86400;
                  $where2['oa.create_time'] = ['between',[$datas['time'][0],$datas['time'][1]]];
              }
              if(!empty($datas['title']))
              {
                  $title2 = $datas['title'];
                  $where2['no.title'] = ['like','%'.$datas['title'].'%'];
              }

          }
          $infos = $this->Oa_send->getadmin($user['id'],$where2);

      $count = $this->Oa_send->BaseSelectCount(['to_id'=>$user['id'],'status'=>1]);

      $data = $this->Oa_notice->getNotice($user['id'],$where1);

      return view('notice/index',['data'=>$data,'count'=>$count,'infos'=>$infos,'time1'=>$time1,'time2'=>$time2,'title1'=>$title1,'title2'=>$title2]);
  }

    /**
     * 发送消息页
     * @return \think\response\View
     */
  public function add()
  {

      $arr =session('adminuser');

      $data = $this->SAdmin->alias('a')
          ->join('__OFFICE__ o','a.office_id = o.id','left')
          ->where('o.delete_time','null')
          ->where(['a.exam_center_id'=>1,'a.status'=>1,'o.status'=>1])
          ->field('o.id,o.name')
          ->group('o.id')
          ->select();
       $info = $this->SOffice->BaseSelect(['status'=>1,'center_id'=>1]);

      return view('',['data'=>$data,'user'=>$arr['id'],'info'=>$info]);
  }

    /**
     * 接收科室--人员消息状态
     * @param Request $request
     * @return \think\response\View
     */
  public function show(Request $request)
  {
      $id= $request->param('id');
      $arr =session('adminuser');
      $office = $this->Oa_send->BaseSelect(['notice_id'=>$id,'user_id'=>$arr['id']],['office_id']);
      $officename = $this->SOffice->column('id,name');
      $adminname = $this->SAdmin->column('id,name');
      $office = array_unique($office);

      foreach ($office as $k=>$v)
      {

          $v['name'] = $officename[$v['office_id']];

          $to = $this->Oa_send->BaseSelect(['office_id'=>$v['office_id'],'notice_id'=>$id,'user_id'=>$arr['id']],['to_id,status,update_time,create_time']);

          foreach ($to as $kk=> $vv)
          {
              $vv['admin_name'] = $adminname[$vv['to_id']];

          }
          $v['admin'] = $to;
      }
      return view('',['data'=>$office]);
  }

    /**
     * 预览消息内容
     * @param Request $request
     */
  public function preview(Request $request)
  {
      $arr =session('adminuser');
      $id = $request->param('id');
      $type = $request->param('type');
      $fujian = $request->param('fujian');
      if($type==2)
      {
           $this->Oa_send->BaseUpdate(['status'=>2],['notice_id'=>$id,'to_id'=>$arr['id']]);
      }
      if($fujian==1)
      {
          $fujianData = $this->SNotFujian->BaseSelect(['notice_id'=>$id]);
          return view('fujian_index',['data'=>$fujianData]);
      }

      $content = $this->Oa_notice->BaseFind(['id'=>$id],['title','content']);
      return view('preview1',['data'=>$content,'type'=>$type]);
  }

    /**
     * 搜索查询
     * @param Request $request
     */
  public function sousuo(Request $request)
  {
      $user =session('adminuser');
      $data = $request->post();
     // dump($data);die;
      $where = [];
      if(!empty($data['time'])){
          $data['time'] = explode('~',$data['time']);
          $data['time'][0] = strtotime($data['time'][0]);
          $data['time'][1] = strtotime($data['time'][1]);
          $where['create_time'] = ['between',[$data['time'][0],$data['time'][1]]];
      }
      if(!empty($datas['title']))
      {
          $where['title'] = ['like','%'.$datas['title'].'%'];
      }
      $datas= $this->Oa_notice->getNotice($user['id'],$where);
      return $datas;
  }
  public function update(Request $request)
  {
      $user =session('adminuser');
      $id = $request->param('id');
      $notice = $this->Oa_notice->BaseFind(['id'=>$id]);
      if($notice['fujian']==1)
      {
         $notice['fujian_id'] = $this->SNotFujian->BaseSelect(['notice_id'=>$id]);
      }
      $data = $this->Oa_send->BaseSelect(['notice_id'=>$id],['to_id']);
      $data = collection($data)->toArray();
      $shujju = $this->Oa_send->alias('oa')
          ->join('__ADMIN__ a','oa.to_id=a.id')
          ->where('oa.notice_id',$id)
          ->where('a.status',1)
          ->field('oa.to_id,a.name,a.office_id')
          ->select();

      $infos = [];
      $office = $this->SAdmin->BaseSelect(['id'=>['in',array_column($data,'to_id')]],['office_id'],'','','office_id');
      $office = collection($office)->toArray();

      $officeName = $this->SOffice->column('id,name');
      $adminArr = $this->SAdmin->column('id,name');

      foreach ($shujju as $k=> $v)
         {

             $res = $this->SAdmin->findCenterUser(['id'=>$v['to_id']]);

             $infos[$k]['id'] = $v['to_id'];
             $infos[$k]['name'] = $adminArr[$v['to_id']];
             $infos[$k]['office_id'] = $v['office_id'];
             $infos[$k]['office_name'] = $officeName[$v['office_id']];
             foreach ($res as $vv)
             {
                 $infos[$k]['role'] = $vv['Irole'];
             }
            // $infos[$k]['role'] = $res[0]['Irole'];




             // $info[$k]['office_id'] = $v['office_id'];
             //$info[$k]['office'] = $officeName[$v['office_id']];



     }


      $info = $this->SOffice->BaseSelect(['status'=>1,'center_id'=>1]);
      $info = collection($info)->toArray();
//      dump($infos);die;

      return view('update',['notice'=>$notice,'notice_id'=>$id,'user'=>$user['id'],'office'=>array_column($office,'office_id'),'infos'=>$infos,'info'=>$info]);

  }
    /**
     * 添加附件
     * @param Request $request
     * @return array
     */
    public function fujian(Request $request)
    {
        $file= $request->file('file');


        if(empty($file))
        {
            return layuiMsg(-1,'未上传文件');
        }

        $temp = explode('.',$_FILES['file']['name']);
        $extension = end($temp);
        $filename = array_shift($temp);
        if(!in_array($extension,array('pdf','docx','xlsx','doc','xls')))
        {
            return layuiMsg(0,'上传文件格式为【pdf,docx,xlsx,doc,xls】');
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
                    $id = $this->SNotFujian->BaseSave($arr);

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
                $id = $this->SNotFujian->BaseSave($arr);
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
            $id = $this->SNotFujian->BaseSave($arr);
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
     * 附件删除
     * @param Request $request
     * @return array
     */
    public function Fujiandelete(Request $request)
    {
        $id = $request->param('id');

        $indo = $this->SNotFujian->where('id',$id)->field('url,notice_id')->find();


        $res = $this->SNotFujian->destroy(['id'=>$id]);
        if($res)
        {
            $info = $this->SNotFujian->where('notice_id',$indo['notice_id'])->select();
            unlink(ROOT_PATH.'public'.$indo['url']);
            if(empty($info))
            {
                $rea = $this->SArticle->where('id',$indo['article_id'])->update(['fujian'=>0]);
                if($rea)
                {
                    return layuiMsg(1,'删除成功');
                }else
                {
                    return layuiMsg(0,'删除失败');
                }
            }else
            {

                return layuiMsg(1,'删除成功');
            }

        }else
        {
            return layuiMsg(0,'删除失败');
        }
    }
}