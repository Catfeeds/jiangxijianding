<?php

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\service\ExamConditions;
use app\common\service\ExamEnrollTable;
use app\common\service\ExamPlan;
use app\common\service\ExamWork;
use app\common\service\ExamWorkLevel;
use app\common\service\Work;
use app\common\service\WorkDirection;
use app\common\service\WorkLevelSubject;
use think\Db;
use think\Request;

/**
 * Class WorkController
 * @package app\api\controller
 */
class WorkController extends BaseApi
{
    /**
     * @var Work
     */
    private $SWork;
    private $SWorkLevelSubject;
    private $SWorkDirection;
    private $SExamWorkLevel;
    private $SExamWork;
    private $SExamPlan;
    private $SExamEnroll;
    private $SExamConditions;

    /**
     * WorkController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->SWork = new Work();
        $this->SWorkLevelSubject = new WorkLevelSubject();
        $this->SWorkDirection = new WorkDirection();
        $this->SExamWorkLevel = new ExamWorkLevel();
        $this->SExamWork = new ExamWork();
        $this->SExamPlan = new ExamPlan();
        $this->SExamEnroll = new ExamEnrollTable();
        $this->SExamConditions = new ExamConditions();
    }

    /**
     * 禁用 启用
     * @user 李海江 2018/9/27~6:37 PM
     */
    public function disable()
    {
        $webData = Request::instance()->post();
        $res = $this->SWork->BaseUpdate(['status' => $webData['status']], ['id' => $webData['id']]);
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }

    /**
     * 删除工种
     * @return array
     * @user 李海江 2018/9/27~8:14 PM
     */
    public function delete()
    {
        $id = Request::instance()->post('id');
        $intResult = $this->SWork->deleteWork(['id' => $id]);
        if ($intResult) {
            return layuiMsg(1, '操作成功');
        } else {
            return layuiMsg(0, '操作失败');
        }
    }

    /**
     * 查找除了自己以外的数据
     * @return int|string
     * @user 李海江 2018/10/8~2:11 PM
     */
    public function findWorkExceptSelf()
    {
        $webData = Request::instance()->post();
        $webData['id'] = ['neq', $webData['id']];
        return $this->SWork->findData($webData);
    }

    /**
     * 修改 新增 工种
     * @return array
     * @user 李海江 2018/10/8~2:11 PM
     */
    public function doeditWork()
    {
        $webData = Request::instance()->post();
        if (empty($webData['id'])) {
            $res = $this->SWork->add($webData);
        } else {
            $res = $this->SWork->edit($webData);
        }
        $msg = $res ? '操作成功' : '操作失败';
        return layuiMsg($res, $msg);
    }

    public function lists()
    {
        $idArray = Request::instance()->post('id');
        $param = ['status' => 1];
        if (!empty($idArray)) {
            $param['id'] = ['in', $idArray];
        }
        $data = $this->SWork->getAlls($param);
        $list = [];
        if (!empty($data)) {
            $list = collection($data)->toArray();
        }
        return layuiMsg(1, '添加成功,请完善证书信息', $list);
    }

    /**
     * 根据work的id 获取工种的方向 work work_direction联查
     * @return array
     * @user 朱颖 9.19
     */
    public function selbyid()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            $webData = $request->post();
            $map['work.id'] = $webData['work_id'];
            $field = "work.*,work_direction.id as did,work_direction.name as dname,work_type.id as tid,work_type.name as typename";
            $data = $this->SWork->selByid($map, $field);
            $dataType = $this->obj->getTypeByid($map);
            $dataLevelSubject = $this->SWorkLevelSubject->BaseSelect($webData, '', '', '', 'level');
            if ($data) {
                return layuiMsg('1', '获取成功', [$data, $dataType, $dataLevelSubject]);
            } else {
                return layuiMsg('-1', '此名称下暂无可报名工种');
            }
        }
    }


    /**
     * 后台获取对应权限的级别
     * @return array
     * @user 朱颖 2018/12/10~14:22
     */
  public function selworklevel()
  {
      $webData = Request::instance()->param();
      $webData['work_id'] = isset($webData['work_id'])?$webData['work_id']:[];
      $workid = implode(",",$webData['work_id']);
//      $data = [];
      $new = [];
      $level = $this->SWorkLevelSubject->selWorktype(['work_id'=>['in',$workid]],['work_level_subject.*,work.work_type_id'],'','','work_id,level');
      $adminuser = session('adminuser');
      $center_type = $adminuser['center_type'];
      foreach ($level as $k=>$v)
      {
          $new[$k]['exam_work_id'] = $v['work_id'];
          $new[$k]['work_level'] = $v['level'];
            //省的a
          if ($v['work_type_id']==config("WorkLevel.a") && $center_type == 1 && $webData['pagetpye']!=4)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.aSheng")))
              {
                  unset($new[$k]);
              }
              //省的b
          }
          if($v['work_type_id']==config("WorkLevel.b") && $center_type == 1)
          {

              if (!in_array($new[$k]['work_level'],config("WorkLevel.bSheng")))
              {
                  unset($new[$k]);
              }
          }
          if($v['work_type_id']==config("WorkLevel.c") && $center_type == 1)
          {

              if (!in_array($new[$k]['work_level'],config("WorkLevel.cSheng")))
              {
                  unset($new[$k]);
              }
          }
          if($v['work_type_id']==config("WorkLevel.z") && $center_type == 1)
          {

              if (!in_array($new[$k]['work_level'],config("WorkLevel.zhuanSheng")))
              {
                  unset($new[$k]);
              }
          }
          if($v['work_type_id']==config("WorkLevel.y") && $center_type == 1)
          {

              if (!in_array($new[$k]['work_level'],config("WorkLevel.yuSheng")))
              {
                  unset($new[$k]);
              }
          }
          if($webData['pagetpye']==4 && $center_type == 1)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.jiSheng")))
              {
                  unset($new[$k]);
              }
          }
          //市a
          if ($v['work_type_id']==config("WorkLevel.a") && $center_type == 2  && $webData['pagetpye']!=4)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.aShi")))
              {
                  unset($new[$k]);
              }
              //市的b
          }
          if($v['work_type_id']==config("WorkLevel.b") && $center_type == 2)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.bShi")))
              {
                  unset($new[$k]);
              }
          }
          if($v['work_type_id']==config("WorkLevel.c") && $center_type == 2)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.cShi")))
              {
                  unset($new[$k]);
              }
          }
          if($v['work_type_id']==config("WorkLevel.z") && $center_type == 2)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.zhuanShi")))
              {
                  unset($new[$k]);
              }
          }
          if($v['work_type_id']==config("WorkLevel.y") && $center_type == 2)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.yuShi")))
              {
                  unset($new[$k]);
              }
          }
          if($webData['pagetpye']==4 && $center_type == 2)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.jiShi")))
              {
                  unset($new[$k]);
              }
              //县a
          }
          if ($v['work_type_id']==config("WorkLevel.a") && $center_type == 3  && $webData['pagetpye']!=4)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.aXian")))
              {
                  unset($new[$k]);
              }
              //县的b
          }
          if($v['work_type_id']==config("WorkLevel.b") && $center_type == 3)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.bXian")))
              {
                  unset($new[$k]);
              }
          }
          if($v['work_type_id']==config("WorkLevel.c") && $center_type == 3)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.cXian")))
              {
                  unset($new[$k]);
              }
          }
          if($v['work_type_id']==config("WorkLevel.z") && $center_type == 3)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.zhuanXian")))
              {
                  unset($new[$k]);
              }
          }
          if($v['work_type_id']==config("WorkLevel.y") && $center_type == 3)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.yuXian")))
              {
                  unset($new[$k]);
              }
          }

          if($webData['pagetpye']==4 && $center_type == 3)
          {
              if (!in_array($new[$k]['work_level'],config("WorkLevel.jiXian")))
              {
                  unset($new[$k]);
              }
          }

      }
      $arr = [];
      foreach ($new as $k=>$v){
          $arr[$v['exam_work_id']][] = $v['work_level'];
      }
//print_r($new);die;
      return layuiMsg('1', '获取成功',$arr);

  }

    /**
     * @根据 exam_plan中的id 获取work中的name
     * @param Request $request
     * @user 朱颖 9.19
     * @return array
     */
    public function selbyworktype()
    {
        $webData = Request::instance()->param();
        if (!$webData['work_type']){
            return layuiMsg('-1', '非法操作');
        }
        $arrWorkType['work_type_id'] = $webData['work_type'];
        $arrWorkType['status'] = 1;
        $data = $this->SWork->BaseSelect($arrWorkType);
        if ($data){
            return layuiMsg('1', '获取成功',$data);
        }else{
            return layuiMsg('-1', '此类型下暂无工种');
        }
    }
//    public function getWorkLevelList(){
//        $webData = Request::instance()->param();
//        if (!$webData['work_type']){
//            return layuiMsg('-1', '非法操作');
//        }
//        $workTypeId = $webData['work_type'];
//        $work_type = 'a';
//        $adminuser = session('adminuser');
//        $center_type = $adminuser['center_type'];
//        if($center_type == 1 ){
//            $center_type = "Sheng";
//        }else if($center_type == 2 ){
//            $center_type = "Shi";
//        }else if($center_type == 3 ){
//            $center_type = "Xian";
//        }
//        $levelArr = config("WorkLevel.".$work_type.$center_type);
//        $levelInt = implode(',',$levelArr);
//        $sql = "select * from `work` w left join work_level_subject wls  on wls.work_id = w.id
//where w.work_type_id = ".$workTypeId." and( wls.`level` in (".$levelInt.") or wls.`level` is NULL)";
//        $level = Db::query($sql);
//        //print_r($sql);
//
//        return layuiMsg('1', '获取成功',$level);
//    }

    /**
     * 根据work_level的id 获取工种的级别  work work_level联查
     * @return array
     * @user 朱颖 9.19
     */
    public function getLevelByid()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            $webData = $request->post();
            $map['work_level_subject.work_id'] = $webData['work_id'];
            $field = "work.*,work_level_subject.level";
            $data = $this->SWork->getLevelByid($map, $field);
            if ($data) {
                return layuiMsg('1', '获取成功', $data);
            } else {
                return layuiMsg('-1', '此名称下暂无可报名级别');
            }
        }
    }

    /**
     * 根据work的id 获取工种的类别  work work_level联查
     * @return array
     * @user xuweiqi 9.26
     */
    public function getTypeByid()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            $webData = $request->post();
            $map['work_id.id'] = $webData['work_id'];
            $data = $this->obj->BaseFind($map);
            if ($data) {
                return layuiMsg('1', '获取成功', $data);
            } else {
                return layuiMsg('-1', '此名称下暂无可报名工种');
            }
        }
    }


    /**
     * 根据work的id 获取工种的方向 work work_direction联查和级别
     * @return array
     * @user xuwieqi 9.28
     */
    public function selDirecLevel()
    {
        $request = Request::instance();
        if ($request->isPost()) {
            $webData = $request->post();
            $map['work.id'] = $webData['work_id'];
            //查询工种的方向
            $data = $this->SWorkDirection->BaseSelect(['work_id' => $webData['work_id'], 'status' => 1]);
            //根据工种查出对应的exam_work的id
            $examWorkId = $this->SExamWork->BaseFind(['work_id' => $webData['work_id'], 'status' => 1, 'type' => 5, 'exam_id' => $webData['alltype_id']], ['id']);
            //查询鉴定计划的工种级别 条件是当前鉴定计划 工种和类型是计划
            $dataType = $this->SExamWorkLevel->BaseSelect(['alltype_id' => $webData['alltype_id'], 'exam_work_id' => $examWorkId['id'], 'type' => 5], '', 'work_level asc');
            $i = 0;
            foreach ($dataType as $k => $v) {
                $dataType[$i]['level_text'] = $v->work_level;
                $i++;
            }
            //获得报名详情
            $arrExamEnroll = $this->SExamPlan->BaseFind(['id' => $webData['alltype_id']]);

            //获得科目
            $join = array(
                ['__SUBJECT__ s', "wls.subject_id=s.id"],
            );
            $subjectNames = $this->SWorkLevelSubject->BaseJoinSelect('wls', $join, ['work_id' => $webData['work_id'], 'wls.status' => 1, 'wls.delete_time' => null], ['s.name'], '', '', 'wls.subject_id');

            $subjectName = '';
            foreach ($subjectNames as $k => $v) {
                $subjectName .= $v->name;
            }
            $issetWork = $this->SExamEnroll->findExamEnroll(['work_id' => $webData['work_id'], 'user_login_id' => session('user')['id']]);
//            if ($data) {
                return layuiMsg('1', '获取成功', [$data, $dataType, $subjectName, $arrExamEnroll]);
//            } else {
//                return layuiMsg('-1', '该工种下暂无可报名级别');
//            }

        }
    }

    /**
     * 根据角色 、工种, 查询 级别 、方向
     * @return array
     * @user xuweiqi
     */
    public function selDirLevelData()
    {
        $data = $this->SWork->dirAndLevel();
        if (!$data) {
            return layuiMsg(-1, '未查到相关信息');
        } else {
            return layuiMsg(1, '', $data);
        }
    }

    /**
     * 条件查询搜索
     * @user xuweiqi
     */
    public function search()
    {
        //接受数据
        $map = $this->request->only(['role', 'work_id', 'dir_id', 'level','yzm'], 'post');
        if(!captcha_check($map['yzm'])){
            return layuiMsg(-2,'验证码错误!');
        }
        unset($map['yzm']);
        //查询
        $res = $this->SExamConditions->showOne($map, ['level', 'content']);
        //返回
        if ($res) {
            if (empty($map['level'])) {
                $res['level'] = (string)0;
                $res['levelChinese'] = null;
            } else {
                $res['level'] = (string)$res['level'];
                $res['levelChinese'] = config('EnrollStatusText.work_level_subject_level')[$map['level']];
                $res['content'] = explode('@', $res['content']);
            }
            return layuiMsg(1, '', $res);
        } else {
            return layuiMsg(-1, '未查询到相关数据');
        }
    }

}