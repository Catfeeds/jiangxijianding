<?php

namespace app\api\controller;
use app\common\service\LearningTopicPaper;
use think\Request;
use app\common\controller\BaseApi;
use think\Session;
use app\common\service\Work;
use app\common\service\WorkDirection;
use app\common\service\LearningTopicSimulation;
class LearningSetVolumeController extends BaseApi
{
    private $Ssetvolume;
    private $SWork;
    private $SWorkDirection;
    private $SLearningTopicSimulation;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->Ssetvolume = new LearningTopicPaper();
        $this->SWork = new Work();
        $this->SWorkDirection = new WorkDirection();
        $this->SLearningTopicSimulation = new LearningTopicSimulation();
    }

    /***
     * @return array
     * @user liuxin 2018/11/26~19:22
     */
    public function selectLevel()
    {
        $map = Request::instance()->post();

        //条件
        $where = [
            'work_id'=>$map['work'],
            'level_id'=>$map['level'],
            'direction_id'=>$map['direction'],
            'range_id'=> $map['range']
        ];

        //条件信息
        Session::set('threeLevel',$where);

        //标题
        if ($range = '1') {
            $range = '练习题库';
        } else {
            $range = '模拟题库';
        }
        switch ($map['level']) {
            case '1':
                $level_name = '高级技师';
                break;
            case '2':
                $level_name = '技师';
                break;
            case '3':
                $level_name = '高级';
                break;
            case '4':
                $level_name = '中级';
                break;
            case '5':
                $level_name = '初级';
                break;
        }
        $title = [
            'work_name'=> $this->SWork->BaseFind(['id'=>$map['work']])['name'],
            'level_name' => $level_name,
            'direction_name' => !empty($map['direction']) ? $this->SWorkDirection->BaseFind(['id'=>$map['direction']])['name'] : '0',
            'range' => $range
        ];

        //合并数组
        $data = array_merge($where, $title);
        return layuiMsg('1','提交成功',$data);
    }

    public function delete()
    {
        if (Request::instance()->isPost())
        {
            $arrData = Request::instance()->post();

            if (!$arrData && empty($arrData['id'])){
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "非法操作";
                return $arrMsg;
            }

            $objDel = $this->Ssetvolume->destroy($arrData);

            if ($objDel){
                $arrMsg['status'] = 1;
                $arrMsg['msg'] = "删除成功";
                return $arrMsg;
            }else{
                $arrMsg['status'] = -1;
                $arrMsg['msg'] = "删除失败";
                return $arrMsg;
            }
        }
    }

    public function release()
    {
        if (Request::instance()->isPost()) {

            $request = Request::instance()->param();

            $data = [
                'id' => $request['id'],
                'exam_name' => $request['exam_name'],
                'pass' => $request['pass_score']
            ];
            //时间
            $time = explode('到',$request['exam_time']);
            $data['start_time'] = strtotime($time[0]);
            $data['stop_time'] = strtotime($time[1]);
            //时长
            if ($request['time_option'] == '1') {
                $data['length_time'] = $request['length_time'];
            } else {
                $data['length_time'] = '0';
            }
            //显示答案和解析
            if (Request::instance()->has('is_answer')) {
                $data['is_answer'] = '1';
            } else {
                $data['is_answer'] = '-1';
            }
            //是否重考
            if (Request::instance()->has('is_retake')) {
                $data['is_retake'] = '1';
            } else {
                $data['is_retake'] = '-1';
            }
            //状态
            if (Request::instance()->has('state')) {
                $data['state'] = '1';
            } else {
                $data['state'] = '-1';
            }

            $result = $this->Ssetvolume->update($data);

            if ($result) {
                return layuiMsg('1','操作成功');
            } else {
                return layuiMsg('-1','操作失败');
            }
        }
    }
}