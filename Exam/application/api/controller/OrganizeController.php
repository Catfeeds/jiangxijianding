<?php
namespace app\api\controller;
use app\common\controller\BaseApi;
use app\common\service\OrganizeWork;
use app\common\service\Organize;
use app\common\service\Work;
use app\common\service\Admin;
use think\captcha\Captcha;
use SendMsm\Msm;
use think\Cache;
use think\Request;

class OrganizeController extends BaseApi{
    private $obj;
    private $SWork;
    private $SOrganize;
    private $Sadmin;


    public function __construct()
    {
        parent::__construct();
        $this->obj = new OrganizeWork();
        $this->SOrganize = new Organize();
        $this->SWork = new Work();
        $this->Sadmin= new Admin();
    }

    //添加机构
    public function add()
    {
        if (Request::instance()->isPost())
        {
            $arrData = input('post.');
            if (isset($arrData['status']) && $arrData['status'] == 1){
                $arrData['status'] = 1;
            }else{
                $arrData['status'] = -1;
            }
            $adminUser=[];
            if (!isset($arrData['fullphone'])) {
                return layuiMsg(-1, "非法操作");
            }
            $adminUser = $arrData['fullphone'];
            $adminUserName = $arrData['fullname'];
            foreach ($adminUser as $k=>$v)
            {
                if(!preg_match("/^1[34578]\d{9}$/", $v)){
                    return layuiMsg(-1, "备用账号格式不正确");
                }
            }
            array_push($adminUser,$arrData['phone']);
            array_push($adminUserName,$arrData['name']);
            $adminUser = array_unique($adminUser);
            $phone = implode(",",$adminUser);
            $addUser['phone'] = ['in',$phone];
//            $addUser['type'] = 2;     //组织
            $arrPhone = $this->Sadmin->BaseSelect($addUser);
            if (!empty($arrPhone))
            {
                return layuiMsg(-1, "账号重复");
            }


            if (isset($arrData['is_institution']) && $arrData['is_institution'] == 1){
                $arrData['is_institution'] = 1;
            }else{
                $arrData['is_institution'] = -1;
            }
            $validata = Validate('app\admin\validate\Organize');
            //场景应用
            if (!$validata->scene('addorganize')->check($arrData)) {
                return layuiMsg(-1, $validata->getError());
            }

            $mapUser['phone'] = $arrData['phone'];
            $arrOrganize = $this->SOrganize->BaseFind($mapUser);
            if ($arrOrganize){
                return layuiMsg(-1,'联系电话已存在');
            }

            $mapCode['code'] = $arrData['code'];
            $arrOrganize = $this->SOrganize->BaseFind($mapCode);
            if ($arrOrganize){
                return layuiMsg(-1,'代码已存在');
            }

            //拼接地址 和地址code
            $address = "";
            $code = "";
            if (!empty($arrData['provids']))
            {
                $address .= $arrData['provid'].",";
                $code = $arrData['provids'];
            }
            if (!empty($arrData['cityids'])){
                $address .= $arrData['cityid'].",";
                $code = $arrData['cityids'];
            }
            if (!empty($arrData['areaids'])){
                $address .= $arrData['areaid'].",";
                $code = $arrData['areaids'];
            }

            $arrData['address'] = rtrim($address,",");
            $arrData['address_code'] = $code;

            $arrData['build_date'] = strtotime($arrData['build_date']);
            $arrData['create_time'] = time();
            $arrData['update_time'] = time();
            $arrData['password'] = md5($arrData['password']."organize");
            unset($arrData['provid']);
            unset($arrData['cityid']);
            unset($arrData['areaid']);
            unset($arrData['standby']);
            //获取ID
            $arrAdmin = session("adminuser");
            $id = $arrAdmin['id'];
            $arrData['create_id'] = $id;
            // 启动事务
            $this->SOrganize->startTrans();
            try {
                $objData = $this->SOrganize->BaseSave($arrData);
                if (!$objData){
                    throw new \Exception('添加失败');
                }
                $addAdminUser = [];
                foreach ($adminUser as $k=>$v)
                {
                    $addAdminUser[$k]['phone'] = $v;
                    $addAdminUser[$k]['type'] = 2;      //组织
                    $addAdminUser[$k]['exam_center_id'] = $objData; //组织id
                    $addAdminUser[$k]['username'] = $adminUserName[$k]; //名称
                    $addAdminUser[$k]['name'] = $adminUserName[$k]; //名称
                    $addAdminUser[$k]['status'] = 1;
                }
//                print_r($addAdminUser);die;
                $arrAdminUser = $this->Sadmin->BaseSaveAll($addAdminUser);
                if (!$arrAdminUser){
                    throw new \Exception('添加失败');
                }
                // 提交事务
                $this->SOrganize->commit();
                return layuiMsg(1,'添加成功');
            } catch (\Exception $e) {
                // 回滚事务
                $this->SOrganize->rollback();
                return layuiMsg(-1,$e->getMessage());
            }
        }
    }

    /**
     * @return array
     * @throws \think\exception\PDOException
     * @user 朱颖 2018/11/6~14:04
     */
  public function delete()
  {
      if (Request::instance()->isPost())
      {
          $arrData = input('post.');
          if (!$arrData && empty($arrData['id'])){
              $arrMsg['status'] = -1;
              $arrMsg['msg'] = "非法操作";
              return $arrMsg;
          }
          // 启动事务
          $this->SOrganize->startTrans();
          try {// 提交事务
          $where['id'] = $arrData['id'];
          $objDel = $this->SOrganize->destroy($where);
          if ( !$objDel )
          {
              throw new \Exception('删除失败');
          }
          $objDelAdmin = $this->Sadmin->destroy(['exam_center_id'=>$where['id'],'type'=>2]);

              if ( !$objDelAdmin )
          {
              throw new \Exception('删除失败');
          }
              //删除另一张表
          $examwork['exam_id'] = $arrData['id'];
          if ($arrData['type'] == 1){
              $examwork['type'] = 6;
          }else if ($arrData['type']== 2){
              $examwork['type'] = 7;
          }else if ($arrData['type'] == 3){
              $examwork['type'] = 4;
          }else{
              $examwork['type'] = 0;
          }
          $objWork = $this->obj->selectWork($examwork,'examwork');
          if ($objWork){
              $objExamWork = $this->obj->deletes($examwork,'examwork');

              if ( !$objExamWork )
              {
                  throw new \Exception('删除失败');
              }
              $examworklevel['alltype_id'] = $arrData['id'];
              $examworklevel['type'] = $examwork['type'];
              $objExamWorkLevel = $this->obj->deletes($examworklevel,'examworklevel');
              if ( !$objExamWorkLevel )
              {
                  throw new \Exception('删除失败');
              }
          }
          $this->SOrganize->commit();
          return layuiMsg(1,'删除成功');
          } catch (\Exception $e) {
              // 回滚事务
              $this->SOrganize->rollback();
              return layuiMsg(-1,$e->getMessage());
          }
//          if ($objDel && $objExamWork && $objExamWorkLevel && $objDelAdmin){
//              return layuiMsg(1, "删除成功");
//          }else{
//              return layuiMsg(-1, "删除失败");
//          }
      }
  }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @user 朱颖 2018/11/6~13:52
     */
  public function update()
  {
      if(Request::instance()->isPost()){
          $arrData = input('post.');
          if (isset($arrData['status']) && $arrData['status'] == 1){
              $arrData['status'] = 1;
          }else{
              $arrData['status'] = -1;
          }
          if (isset($arrData['is_institution']) && $arrData['is_institution'] == 1){
              $arrData['is_institution'] = 1;
          }else{
              $arrData['is_institution'] = -1;
          }

          $validata = Validate('app\admin\validate\Organize');
          //场景应用
          if (!$validata->scene('uporganize')->check($arrData)) {
              return layuiMsg('-1', $validata->getError());
          }

          $mapCode['code'] = $arrData['code'];
          $arrOrganize = $this->SOrganize->find($mapCode);
//          print_r($arrOrganize);die;
          if ($arrOrganize && $arrOrganize['id'] != $arrData['id']){
              return layuiMsg('-1', '代码已存在');

          }

          //拼接地址 和地址code
          $address = "";
          $code = "";
          if (!empty($arrData['provids']))
          {
              $address .= $arrData['provid'].",";
              $code = $arrData['provids'];
          }
          if (!empty($arrData['cityids'])){
              $address .= $arrData['cityid'].",";
              $code = $arrData['cityids'];
          }
          if (!empty($arrData['areaids'])){
              $address .= $arrData['areaid'].",";
              $code = $arrData['areaids'];
          }

          $arrData['address'] = rtrim($address,",");
          $arrData['address_code'] = $code;

          if (!$arrData['provids']){
              return layuiMsg('-1', '请选择地址');
          }

          $arrData['update_time'] = time();
          $arrData['build_date'] = strtotime($arrData['build_date']);

          unset($arrData['provid']);
          unset($arrData['cityid']);
          unset($arrData['areaid']);

          $arrExamWork = [];
          if (!isset($arrData['work']))
          {
              return layuiMsg('-1', '请选择工种');
          }
          $work_id = $arrData['work'];
          unset($arrData['work']);




          $workid = implode(",",$work_id);
          $objWork = $this->SWork->BaseSelect(['id'=>['in',$workid]]);

//          print_r($objWork);die;

          $type = 0;
          //鉴定所
          if ($arrData['type'] == 1){
              $type = 6;
          }
          //院校
          if ($arrData['type'] == 2){
              $type = 7;
          }
          //机构
          if ($arrData['type'] == 3){
              $type = 4;
          }

          foreach ($work_id as $key=>$val){
              $arrExamWork[$key]['work_id'] = $val;
//              $arrExamWork[$key]['work_type'] = $webData['work_type'];
              $arrExamWork[$key]['type'] = $type;
              $arrExamWork[$key]['status'] = $arrData['status'];
              $arrExamWork[$key]['exam_id'] = $arrData['id'];
              $arrExamWork[$key]['create_time'] = time();
              $arrExamWork[$key]['update_time'] = time();
          }

          foreach ($arrExamWork as $k=>$v)
          {
              foreach ($objWork as $key=>$val){
                  if ($v['work_id'] == $val['id']){
                      $arrExamWork[$k]['work_name'] = $val['name'];
                      $arrExamWork[$k]['work_type'] = $val['work_type_id'];
                  }
              }
          }

          $level = $arrData['level'];
          unset($arrData['level']);
          foreach ($level as $k=>$v){
              foreach ($v as $key=>$val){
                  $a[]['work_level'][$k] = $val;
                  $new[]['work_level'] = $val;
                  foreach ($a as $kk=>$vv){
                      foreach ($vv['work_level'] as $ke =>$va ){
                          if ($va == $val && $ke == $k ){
                              $new[$kk]['exam_work_id'] = $k;
                              $new[$kk]['type'] = $type;
                              $new[$kk]['alltype_id'] = $arrData['id'];
                              $new[$kk]['create_time'] = time();
                              $new[$kk]['update_time'] = time();

                          }
                      }

                  }
              }

          }
          $where['id'] = $arrData['id'];
          unset($arrData['id']);

          $updateData = $this->obj->updateOrganize($where,$arrData,$arrExamWork,$new,$type);
          return $updateData;
      }
  }

    /**
     * 机构登录
     * @return mixed
     * @user 朱颖 2018/11/6~14:04
     */
  public function dologin()
  {
      if (Request::instance()->isPost()) {
          $arrData = input('post.');
          $code = Cache::get($arrData['phone']);
          // $res = check_code($arrData['yzm'],$arrData['phone']);
          // dump($res);die;
          if($code != $arrData['yzm'])
          {
            $arrMsg['status'] = -2;
            $arrMsg['msg'] = "验证码错误";
            return $arrMsg;
          }
          //数据验证
          // $validata = Validate('app\organize\validate\User');

          // //场景应用
          // if (!$validata->scene('login')->check($arrData)) {
          //     $arrMsg['status'] = -1;
          //     $arrMsg['msg'] = $validata->getError();
          //     return $arrMsg;
          // }
          $map['code'] = $arrData['code'];
          $map['phone'] = $arrData['phone'];
          $map['status'] = 1;

          $data = $this->SOrganize->BaseFind($map);
          if (empty($data)) {
              $arrMsg['status'] = -2;
              $arrMsg['msg'] = "账号或密码错误";
              //账号或密码错误
              return $arrMsg;
          } else {
              $sdata = [
                  'id' => $data['id'],
                  'username' => $data['username'],
                  'phone' => $data['phone'],
                  'type' => $data['type'],
                  'is_institution'=> $data['is_institution'],
              ];
              session('organizeuser', $sdata);
              $arrMsg['status'] = 1;
              $arrMsg['msg'] = "登录成功";
              //账号或密码错误
              return $arrMsg;
          }
      }
  }

    /**
     * 机构修改密码
     * @return mixed
     * @user 朱颖 2018/11/6~14:05
     */
    public function updatepage()
    {
        if (Request::instance()->isPost()) {
            $arrData = input('post.');
            $validata = Validate('app\organize\validate\User');

            //场景应用
            if (!$validata->scene('updatepwd')->check($arrData)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = $validata->getError();
                return $arrMsg;
            }
            if ($arrData['newpwd'] !== $arrData['conpwd'])
            {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "两次密码输入不一致";
                //两次密码输入不一致
                return $arrMsg;
            }
            $map['password'] = md5($arrData['oldpwd']."organize");
            $map['status'] = 1;
            $arrDataOne = $this->SOrganize->BaseFind($map);
//            print_r($arrDataOne);die;
            if (empty($arrDataOne)) {
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "旧密码错误";
                //账号或密码错误
                return $arrMsg;
            } else {
                $upData['update_time'] = date("Y-m-d H:i:s");
                $upData['password'] = md5($arrData['newpwd']."organize");;
                $upMap['id'] = $arrDataOne['id'];
                $objUpdate = $this->SOrganize->BaseUpdate($upData,$upMap);
                if ($objUpdate)
                {
                    $arrMsg['status'] = 1;
                    $arrMsg['msg'] = "修改成功,请重新登录";
//                    session('adminuser', null);
                    //账号或密码错误
                    return $arrMsg;
                }else{
                    $arrMsg['status'] = -1;
                    $arrMsg['msg'] = "修改失败";
                    //账号或密码错误
                    return $arrMsg;
                }
            }
        }
    }

    /**
     * 获取当前用户存不存在
     * @param Request $request
     * @return mixed
     * @user 朱颖 2018/9/14~下午16:32
     */
    public function findAdmin(Request $request)
    {
        if (Request::instance()->isPost())
        {
            $arrData = input('post.');
            $arrOrganize = $this->SOrganize->BaseFind($arrData);
//            print_r($arrOrganize);die;
            if ($arrOrganize){
                return 1;
            }
        }

    }

    /**
     * 退出登录
     * @return int
     * @user 朱颖 2018/9/14~下午16:32
     */
    public function loginOut()
    {
        session('organizeuser', null);
        return 1;
    }

    /**
     * [checkPhone 检查手机号]
     * @return [type] [description]
     */
    public function checkPhone()
    {
      $data=input('post.');
      //查询条件 判断是否存在已注册手机号
      $dataFind= ['mobile'=>$data['mobile']];
      //从数据库根据条件查询
      $UserLoginRes=$this->SOrganize->BaseFind($dataFind);
      //结果为空返回0(没有注册)  不为空返回1(有数据已注册)
      if(empty($UserLoginRes)){
          return layuiMsg(-1,'手机号不存在');
      }
    }

    /**
     * [updatePass 手机号找回密码修改]
     * @return [type] [description]
     */
    public function updatePass()
    {
        $data = Request()->post();
        $updateData['password'] = md5($data['newPass'].'organize');
        $res = $this->SOrganize->BaseUpdate($updateData,['phone'=>$data['mobile']]);
        if($res)
        {
              return layuiMsg(1,"更新成功");
        } else {
                return layuiMsg(-1,'更新失败');
        }
    }

    /**
     * [codePhone 查询机构代码与手机是否匹配 发送验证码]
     * @return [type] [description]
     */
    public function codePhone()
    {
      $data = Request()->post();
      $result = $this->SOrganize->BaseFind(['code'=>$data['code'],'phone'=>$data['phone'],'status'=>1]);
      if(!$result)
      {
        return layuiMsg(-1,'机构代码与手机号不匹配');
      }

      $res = Msm::sendMessage($data['phone'],0);
      if (!$res['flag']) {
          return layuiMsg(0,$res['message']);
      } else {
          return layuiMsg(1,'发送成功');
      }


    }

    /**
     * 根据关键字搜索机构
     * @param Request $request
     */
    public function sou(Request $request)
    {
//        $data = $request->post();
//        $where['name'] = ['like','%'.$data['data'].'%'];
//        $infos = $this->SOrganize->where()->select();
//        dump($infos);die;
    }

}