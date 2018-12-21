<?php
/**
 * Created by PhpStorm.
 * User: cisco
 * Date: 2018/12/2
 * Time: 16:33
 */
namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\Admin;
use app\common\service\AdminRole;
use app\common\service\CmsNoticeFujian;
use app\common\service\OaNotice;
use app\common\service\OaSend;
use app\common\service\Office;
use app\common\service\Role;
use think\helper\Str;
use think\Request;

class OaSendController extends BaseApi
{
    protected $SOasend;
    protected $SAdmin;
    protected $SOanotice;
    protected $SOffice;
    protected $SAdminRole;
    protected $SRole;
    protected $SFujian;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->SOasend = new OaSend();
        $this->SAdmin = new Admin();
        $this->SOanotice = new OaNotice();
        $this->SOffice = new Office();
        $this->SAdminRole = new AdminRole();
        $this->SRole = new Role();
        $this->SFujian = new CmsNoticeFujian();
    }
    public function add(Request $request)
    {
         $data = $request->post();


//        dump($data);die;
//         $arr['title'] =$data['title'];
//        $arr['content'] = $data['content'];
//        $arr['user_id'] = $data['user_id'];
//        $arrSend = [];$i = 0;
//        $arrOffice=[];
//        $arrToid = explode(',',$data['office']);
//        if(!empty($data['notice_id']))
//        {
//            $officeids = $this->SOasend->BaseSelect(['notice_id'=>$data['notice_id']],['office_id']);
//            foreach ($officeids as $vvv)
//            {
//                $arrOffice[] =(string)$vvv['office_id'];
//            }
//            $arrOffice = array_unique($arrOffice);
//
//            foreach ($arrToid as $k=>$vv)
//            {
//                if(in_array($vv,$arrOffice))
//                {
//                    $arroo[] = $vv;
//                    unset($arrToid[$k]);
//                }
//            }
//
//            $res = $this->SOanotice->BaseUpdate($arr,['id'=>$data['notice_id']]);
//            if(!empty($arroo)){
//               foreach ($arroo as $kk=>$aaa)
//               {
//
//                   $this->SOasend->where(['notice_id'=>$data['notice_id'],'office_id'=>$aaa])->update(['status'=>1]);
//
//                       foreach ($arrOffice as $kkk=>$fff)
//                       {
//                           if($aaa==$fff)unset($arrOffice[$kkk]);
//                       }
//
//               }
//
//               if(!empty($arrOffice)){
//                   foreach ($arrOffice as $ooo)
//                   {
//                       $this->SOasend->where(['notice_id'=>$data['notice_id'],'office_id'=>$ooo])->update(['delete_time'=>time()]);
//                   }
//               }
//            }else{
//                $this->SOasend->where(['notice_id'=>$data['notice_id']])->update(['delete_time'=>time()]);
//            }
//            $ress = $data['notice_id'];
//        }else{
//            $ress = $this->SOanotice->BaseSave($arr);
//        }
//        if(!empty($arrToid)){
//            $to_id = $this->SAdmin->BaseSelect(['office_id'=>['in',$arrToid]],['id,office_id']);
//
//        }else{
//            return layuiMsg(1,'修改成功');
//        }
//
//        if($ress)
//        {
//            foreach ($to_id as $v)
//            {
//                if($v['id']!=$data['user_id'])
//                {
//                    $arrSend[$i]['user_id'] = $data['user_id'];
//                    $arrSend[$i]['to_id'] = $v['id'];
//                    $arrSend[$i]['office_id'] = $v['office_id'];
//                    $arrSend[$i]['status'] = 1;
//                    $arrSend[$i]['notice_id'] = $ress;
//                    $i++;
//                }
//
//            }
//            $res = $this->SOasend->saveAll($arrSend);
//            if($res)
//            {
//                return layuiMsg(1,'发送成功');
//            }
//        }
//        return layuiMsg(0,'发送失败');

        if(!isset($data['admin_id']))
        {
            return layuiMsg(0,'发送人不能为空');
        }
        $notice['title'] = $data['title'];
        $notice['user_id'] = $data['user_id'];
        $notice['content'] = $data['content'];
        $notice['fujian'] = $data['fujian'];
        $res = $this->SOanotice->BaseSave($notice);

        if($res)
        {
            if($data['fujian']==1)
            {
                $this->SFujian->BaseUpdate(['notice_id'=>$res],['id'=>['in',$data['fujia_id']]]);
            }

            $i =0;
            foreach ($data['admin_id'] as $v)
            {
                $office_id = $this->SAdmin->BaseFind(['id'=>$v],['office_id']);
                $send[$i]['office_id'] = $office_id['office_id'];
                $send[$i]['to_id'] = $v;
                $send[$i]['user_id'] = $data['user_id'];
                $send[$i]['notice_id'] = $res;
                $send[$i]['status'] = 1;
                $i++;
            }
            $ress = $this->SOasend->BaseSaveAll($send);
            if($ress)
            {
                return layuiMsg(1,'发送成功');
            }
            return layuiMsg(0,'发送失败');
        }


    }
   public function update(Request $request)
   {
       $data = $request->post();

       $arrTO = $this->SOasend->BaseSelect(['notice_id'=>$data['notice_id']],['to_id']);
       $arrTO = array_column($arrTO,'to_id');
       foreach ($data['admin_id'] as $v)
       {
           if(in_array($v,$arrTO))
           {
               $arrOn[] = $v;
               if(count($arrOn)<=count($arrTO))
               {
                   $arrCa = array_diff($arrTO,$arrOn);
               }
           }else
               {
                   $arrNo[] = $v;
               }
       }

       if(!empty($arrCa))
       {
           $this->SOasend->BaseUpdate(['delete_time'=>time()],['to_id'=>['in',$arrCa],'notice_id'=>$data['notice_id']]);

       }

       if(empty($arrOn))
       {
           $this->SOasend->BaseUpdate(['delete_time'=>time()],['to_id'=>['in',$arrTO],'notice_id'=>$data['notice_id']]);

       }else
           {
               $this->SOasend->BaseUpdate(['status'=>1],['to_id'=>['in',$arrOn],'notice_id'=>$data['notice_id']]);

           }
       if(!empty($arrNo))
       {
           foreach ($arrNo as $k=> $vv)
           {

               $info['to_id'] =$vv;
               $info['notice_id'] = $data['notice_id'];
               $info['user_id'] = $data['user_id'];
               $info['status'] = 1;
               $this->SOasend->BaseSave($info);
           }
       }
       $notice['title'] = $data['title'];
       $notice['user_id'] = $data['user_id'];
       $notice['content'] = $data['content'];
       $notice['fujian'] = $data['fujian'];
       $res = $this->SOanotice->BaseUpdate($notice,['id'=>$data['notice_id']]);
       if($res)
       {
           if($data['fujian']==1 && isset($data['fujia_id']))
           {
               $this->SFujian->BaseUpdate(['notice_id'=>$data['notice_id']],['id'=>['in',$data['fujia_id']]]);
           }
           return layuiMsg(1,'重新发送成功');
       }
       return layuiMsg(0,'重新发送失败');
   }
    /**
     * 科室列表
     * @return mixed
     */
//    public function keshi(Request $request)
//    {
//        $id = $request->param('notice_id');
//        $office = $this->SOasend->BaseSelect(['notice_id'=>$id],['office_id']);
//        $office = array_unique_key($office,'office_id');
//
//        $data = $this->SOffice->where(['center_id'=>1,'status'=>1])->field('id,name')->select();
//        $i = 0;
//        $office_id = [];
//        if(!empty($office)){
//            foreach ($office as $vv){
//                $office_id[] = $vv['office_id'];
//            }
//        }
//
//        foreach ($data as $v)
//        {
//          $infos = $this->SAdmin
//              ->where('office_id',$v['id'])
//              ->where(['status'=>1])
//              ->field('id,name')->select();
//
//
//          if(empty($infos))
//          {
//              $infos[$i]['name'] = '';
//              $infos[$i]['id'] = '';
//          }
//          $k = 0;
//
//          foreach ($infos as $vv)
//          {
//              $arrinfos[$i][$k]['name'] = $vv['name'];
//              $arrinfos[$i][$k]['value'] =$v['id'].'-'. $vv['id'];
////              $roleinfo = $this->SAdminRole->alias('r')
////                  ->join('__ROLE__ ro','r.role.id=ro.id','left')
////                  ->where(['ro.status'=>1,'ro.delete_time'=>NULl])
////                  ->field('ro.name,r.')
//              $k++;
//          }
//
//            $arrdata[$i]['name'] = $v['name'];
//            $arrdata[$i]['value'] = $v['id'].'-';
//            if(!empty($office_id) && in_array($v['id'],$office_id) )
//            {
//                $arrdata[$i]['selected'] = 'selected';
//            }
//             $arrdata[$i]['children'] = $arrinfos[$i];
//            $i++;
//        }
//        echo "<pre>";
//        print_r($infos);die;
////           echo '<pre>';
////          print_r($arrdata);
//        return  $arrdata;
//    }
        public function admin(Request $request)
        {
            $office = $request->post();

            $admins = $this->SAdmin->BaseSelect(['office_id'=>$office['office_id'],'status'=>1,'id'=>['<>',$office['user']]],['id','name']);
            $info = [];
            $officeName = $this->SOffice->column('id,name');

            foreach ($admins as $k=> $v)
            {
                $res = $this->SAdmin->findCenterUser(['id'=>$v['id']]);


                $info[$k]['id'] = $v['id'];
               $info[$k]['name'] = $v['name'];
                $info[$k]['office_id'] = $office['office_id'];
              $info[$k]['office'] = $officeName[$office['office_id']];
                foreach ($res as $vv)
                {
                    $info[$k]['role'] = $vv['Irole'];
                }

            }

            return $info;

        }
}